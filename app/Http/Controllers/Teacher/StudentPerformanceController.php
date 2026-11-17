<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ExamAttempt;
use App\Models\StudentPerformanceAnalysis;
use App\Models\User;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Throwable;

class StudentPerformanceController extends Controller
{
    public function show(User $student)
    {
        abort_unless($student->role === 'student', 404);

        $teacherId = auth()->id();

        $hasClassAccess = $student->enrolledClasses()
            ->whereHas('teacher', fn ($query) => $query->whereKey($teacherId))
            ->exists();

        $hasExamAccess = $student->attempts()
            ->whereHas('exam', fn ($query) => $query->where('created_by', $teacherId))
            ->exists();

        abort_unless($hasClassAccess || $hasExamAccess, 403);

        $attempts = ExamAttempt::with([
    'exam.subject',
    'answers.question',
])
    ->where('student_id', $student->id)
    ->where('status', 'completed')
    ->whereHas('exam', fn ($query) => $query->where('created_by', $teacherId))
    ->latest('submitted_at')
    ->get();
$latestAttemptIds = $attempts
    ->groupBy('exam_id')
    ->map(fn ($examAttempts) => $examAttempts->first()->id)
    ->values()
    ->all();
// PUT THE TOPIC CALCULATION HERE
$topicStatsByAttempt = $attempts->mapWithKeys(function ($attempt) {
    $topics = $attempt->answers
        ->filter(fn ($answer) => trim((string) $answer->question?->topic) !== '')
        ->groupBy(fn ($answer) => trim((string) $answer->question->topic))
        ->map(function ($answers, $topic) {
            $total = $answers->count();
            $correct = $answers->where('is_correct', true)->count();

            return [
                'topic' => $topic,
                'correct' => $correct,
                'total' => $total,
                'percentage' => $total > 0
                    ? round(($correct / $total) * 100, 1)
                    : 0,
            ];
        })
        ->sortBy('percentage')
        ->values();

    return [$attempt->id => $topics];
});

$analyses = StudentPerformanceAnalysis::with('attempt.exam.subject')
    ->where('student_id', $student->id)
    ->where('teacher_id', $teacherId)
    ->latest()
    ->get();

return view('teacher.students.performance', compact(
    'student',
    'attempts',
    'analyses',
    'topicStatsByAttempt',
        'latestAttemptIds'
));
    }

    public function storeExam(
    Request $request,
    User $student,
    ExamAttempt $attempt
) {
    abort_unless((int) $attempt->student_id === (int) $student->id, 404);
    abort_unless($attempt->status === 'completed', 403);
    abort_unless(
        (int) $attempt->exam?->created_by === (int) auth()->id(),
        403
    );

    $validated = $request->validate([
        'method' => ['required', 'in:ai,manual'],
        'content' => ['required', 'string', 'min:5', 'max:10000'],
    ]);

    StudentPerformanceAnalysis::create([
        'student_id' => $student->id,
        'teacher_id' => auth()->id(),
        'exam_attempt_id' => $attempt->id,
        'scope' => 'exam',
        'method' => $validated['method'],
        'content' => trim($validated['content']),
        'sent_at' => now(),
    ]);

    return back()->with('success', 'Exam feedback sent to the student.');
}

public function storeOverall(
    Request $request,
    User $student
) {
    abort_unless($student->role === 'student', 404);

    abort_if(
        $this->teacherAttempts($student)->isEmpty(),
        422,
        'This student has no completed exams from your classes yet.'
    );

    $validated = $request->validate([
        'method' => ['required', 'in:ai,manual'],
        'content' => ['required', 'string', 'min:5', 'max:10000'],
    ]);

    StudentPerformanceAnalysis::create([
        'student_id' => $student->id,
        'teacher_id' => auth()->id(),
        'exam_attempt_id' => null,
        'scope' => 'overall',
        'method' => $validated['method'],
        'content' => trim($validated['content']),
        'sent_at' => now(),
    ]);

    return back()->with('success', 'Overall feedback sent to the student.');
}

    public function updateDraft(Request $request, StudentPerformanceAnalysis $analysis)
    {
        $this->authorizeDraft($analysis);

        $validated = $request->validate([
            'content' => ['required', 'string', 'min:5', 'max:10000'],
        ]);

        $analysis->update([
            'content' => trim($validated['content']),
            'method' => 'manual',
        ]);

        return back()->with('success', 'Draft updated.');
    }

    public function send(StudentPerformanceAnalysis $analysis)
    {
        $this->authorizeDraft($analysis);

        $analysis->update([
            'sent_at' => now(),
        ]);

        return back()->with('success', 'Analysis sent to the student in AI-Q.');
    }

    private function authorizeDraft(StudentPerformanceAnalysis $analysis): void
    {
        abort_unless((int) $analysis->teacher_id === (int) auth()->id(), 403);
        abort_if($analysis->sent_at !== null, 403, 'This analysis has already been sent.');
    }

    private function teacherAttempts(User $student): Collection
    {
        return ExamAttempt::with(['exam.subject', 'answers.question'])
            ->where('student_id', $student->id)
            ->where('status', 'completed')
            ->whereHas('exam', fn ($query) => $query->where('created_by', auth()->id()))
            ->orderBy('submitted_at')
            ->get();
    }

    private function generateExamAnalysis(ExamAttempt $attempt, GeminiService $gemini): string
    {
        $attempt->loadMissing(['exam.subject', 'answers.question']);

        $correct = $attempt->answers->where('is_correct', true)->count();
        $answered = $attempt->answers->count();
        $percentage = ($attempt->total_score ?? 0) > 0
            ? round(($attempt->score / $attempt->total_score) * 100, 1)
            : 0;

        $topics = $attempt->answers
            ->filter(fn ($answer) => trim((string) $answer->question?->topic) !== '')
            ->groupBy(fn ($answer) => trim((string) $answer->question->topic))
            ->map(fn ($answers, $topic) => [
                'topic' => $topic,
                'correct' => $answers->where('is_correct', true)->count(),
                'total' => $answers->count(),
            ])->values()->all();

        $prompt = "Write supportive, specific feedback for a student based only on this exam data. Do not diagnose the student or make claims unsupported by the data. Mention strengths, topics to review, and 2-3 practical next steps. Address the student directly. Do not include a name or invent facts.\n\n"
            . "Exam: " . ($attempt->exam?->title ?? 'Exam') . "\n"
            . "Subject: " . ($attempt->exam?->subject?->name ?? 'Not specified') . "\n"
            . "Score: {$attempt->score} / {$attempt->total_score} ({$percentage}%)\n"
            . "Correct answers: {$correct} of {$answered}\n"
            . "Topic results: " . json_encode($topics, JSON_UNESCAPED_UNICODE);

        return $this->cleanAiText($gemini->generate($prompt));
    }

    private function generateOverallAnalysis(Collection $attempts, GeminiService $gemini): string
    {
        $scores = $attempts->map(function ($attempt) {
            return ($attempt->total_score ?? 0) > 0
                ? round(($attempt->score / $attempt->total_score) * 100, 1)
                : 0;
        });

        $examResults = $attempts->map(function ($attempt, $index) use ($scores) {
            return [
                'exam' => $attempt->exam?->title ?? 'Exam',
                'subject' => $attempt->exam?->subject?->name ?? 'Not specified',
                'percentage' => $scores[$index],
                'submitted_at' => $attempt->submitted_at?->toDateString(),
            ];
        })->values()->all();

        $average = round($scores->average(), 1);

        $prompt = "Write supportive overall academic feedback for a student using only these completed exam results. Describe progress or patterns only when the data supports them. Include strengths, areas to review, and 2-3 practical next steps. Do not diagnose the student, include a name, or invent facts. Address the student directly.\n\n"
            . "Average score: {$average}%\n"
            . "Completed exam results: " . json_encode($examResults, JSON_UNESCAPED_UNICODE);

        return $this->cleanAiText($gemini->generate($prompt));
    }
    public function showResult(ExamAttempt $attempt)
{
    abort_if($attempt->status !== 'completed', 404);

    abort_unless(
        (int) $attempt->exam?->created_by === (int) auth()->id(),
        403
    );

    $attempt->load([
    'exam.subject',
    'exam.questions.options',
    'answers.question.options',
]);

    return view('student.exam.result', [
        'attempt' => $attempt,
        'teacherView' => true,
    ]);
}
public function generateExamDraft(
    User $student,
    ExamAttempt $attempt,
    GeminiService $gemini
) {
    abort_unless((int) $attempt->student_id === (int) $student->id, 404);
    abort_unless($attempt->status === 'completed', 403);
    abort_unless(
        (int) $attempt->exam?->created_by === (int) auth()->id(),
        403
    );

    try {
        return response()->json([
            'content' => $this->generateExamAnalysis($attempt, $gemini),
        ]);
    } catch (Throwable $exception) {
        Log::warning('AI student feedback draft failed.', [
            'attempt_id' => $attempt->id,
            'message' => $exception->getMessage(),
        ]);

        return response()->json([
            'message' => 'AI could not generate the draft. Check the Gemini API key or write the feedback manually.',
        ], 422);
    }
}
public function generateOverallDraft(
    User $student,
    GeminiService $gemini
) {
    abort_unless($student->role === 'student', 404);

    $attempts = $this->teacherAttempts($student);

    abort_if(
        $attempts->isEmpty(),
        422,
        'This student has no completed exams from your classes yet.'
    );

    try {
        return response()->json([
            'content' => $this->generateOverallAnalysis($attempts, $gemini),
        ]);
    } catch (Throwable $exception) {
        Log::warning('Overall student analysis generation failed.', [
            'student_id' => $student->id,
            'message' => $exception->getMessage(),
        ]);

        return response()->json([
            'message' => 'AI could not generate the draft. Check the Gemini API key or write feedback manually.',
        ], 422);
    }
}
private function cleanAiText(string $text): string
{
    // Turn Markdown headings into plain text headings.
    $text = preg_replace('/^\s{0,3}#{1,6}\s*/m', '', $text);

    // Remove common Markdown formatting characters.
    $text = str_replace(['**', '__', '~~', '`', '*'], '', $text);

    // Keep link labels, discard Markdown link syntax.
    $text = preg_replace('/\[([^\]]+)\]\([^)]+\)/', '$1', $text);

    // Replace Markdown list markers with plain bullets.
    $text = preg_replace('/^\s*[-+]\s+/m', '• ', $text);

    $text = str_replace(["\r\n", "\r"], "\n", $text);
    $text = preg_replace('/[ \t]+/', ' ', $text);
    $text = preg_replace('/\n{3,}/', "\n\n", $text);

    return trim($text);
}
public function resetAttempt(ExamAttempt $attempt)
{
    abort_unless(
        (int) $attempt->exam?->created_by === (int) auth()->id(),
        403
    );

    abort_unless($attempt->status === 'completed', 422);

    $latestCompletedAttempt = ExamAttempt::where('exam_id', $attempt->exam_id)
        ->where('student_id', $attempt->student_id)
        ->where('status', 'completed')
        ->latest('id')
        ->first();

    abort_unless(
        $latestCompletedAttempt &&
        (int) $latestCompletedAttempt->id === (int) $attempt->id,
        422,
        'Only the student’s latest completed attempt can be reset.'
    );

    $attempt->update(['retake_allowed' => true]);

    app(\App\Services\ActivityLogger::class)->log(
        'exam.retake_allowed',
        'Teacher allowed a student to retry an examination.',
        $attempt,
        ['exam_id' => $attempt->exam_id]
    );

    return back()->with(
        'success',
        'Retry allowed. The student’s completed result remains in their history.'
    );
}
}