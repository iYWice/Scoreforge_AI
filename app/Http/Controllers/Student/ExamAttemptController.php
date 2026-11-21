<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Services\AcademicReadinessService;
use App\Services\ActivityLogger;
use App\Services\PerformancePredictionService;
use App\Services\RecommendationService;
use App\Services\StudentAnalyticsService;
use App\Services\StudentRankingService;
use Illuminate\Http\Request;

class ExamAttemptController extends Controller
{
    public function enterCode()
    {
        return view('student.enter-code');
    }

    public function showCodeForm()
    {
        return view('student.enter-code');
    }

    public function startExam(Request $request)
    {
        $request->validate([
            'exam_code' => 'required',
        ]);

        $exam = Exam::where(
            'exam_code',
            strtoupper($request->exam_code)
        )
            ->where(
                'status',
                'published'
            )
            ->first();

        if (! $exam) {

            return back()->with(
                'error',
                'Invalid or unavailable exam code.'
            );
        }

        if (! $exam->isWithinAvailabilityWindow()) {
            return redirect()
                ->route('student.dashboard')
                ->with(
                    'error',
                    $exam->availabilityMessage()
                );
        }

        $this->authorizeExam($exam);

        $ongoingAttempt = ExamAttempt::where('exam_id', $exam->id)
            ->where('student_id', auth()->id())
            ->where('status', 'ongoing')
            ->latest('id')
            ->first();

        if ($ongoingAttempt) {
            return redirect()->route('student.exam.take', $ongoingAttempt->id);
        }

        $latestCompletedAttempt = ExamAttempt::where('exam_id', $exam->id)
            ->where('student_id', auth()->id())
            ->where('status', 'completed')
            ->latest('id')
            ->first();

        if ($latestCompletedAttempt && ! $latestCompletedAttempt->retake_allowed) {
            return redirect()->route(
                'student.exam.result',
                $latestCompletedAttempt->id
            );
        }

        return view(
            'student.exam.instructions',
            compact('exam')
        );
    }

    /*
    Start Exam
    */
    public function beginExam(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'agreement' => 'accepted',
        ]);

        $exam = Exam::where('id', $request->exam_id)
            ->where('status', 'published')
            ->first();

        if (! $exam) {
            return redirect()
                ->route('student.dashboard')
                ->with(
                    'error',
                    'This exam is no longer available.'
                );
        }

        if (! $exam->isWithinAvailabilityWindow()) {
            return redirect()
                ->route('student.dashboard')
                ->with(
                    'error',
                    $exam->availabilityMessage()
                );
        }

        $this->authorizeExam($exam);

        // Check if the student already has an attempt
        $ongoingAttempt = ExamAttempt::where('exam_id', $exam->id)
            ->where('student_id', auth()->id())
            ->where('status', 'ongoing')
            ->latest('id')
            ->first();

        if ($ongoingAttempt) {
            return redirect()->route('student.exam.take', $ongoingAttempt->id);
        }

        $latestCompletedAttempt = ExamAttempt::where('exam_id', $exam->id)
            ->where('student_id', auth()->id())
            ->where('status', 'completed')
            ->latest('id')
            ->first();

        if ($latestCompletedAttempt) {
            if (! $latestCompletedAttempt->retake_allowed) {
                return redirect()->route(
                    'student.exam.result',
                    $latestCompletedAttempt->id
                );
            }

            // Consume the teacher's one-time retry permission.
            $latestCompletedAttempt->update(['retake_allowed' => false]);
        }

        // Create attempt only when none exists
        $attempt = ExamAttempt::create([
            'exam_id' => $exam->id,
            'student_id' => auth()->id(),
            'started_at' => now(),
            'status' => 'ongoing',
        ]);

        app(ActivityLogger::class)->log(
            'exam.started',
            'Student started an examination.',
            $attempt,
            ['exam_id' => $exam->id]
        );

        return redirect()->route(
            'student.exam.take',
            $attempt->id
        );
    }

    /*
    Take Exam
    */
    public function takeExam(ExamAttempt $attempt)
    {
        $this->authorizeAttempt($attempt);

        if (! $attempt->exam->isWithinAvailabilityWindow()) {
            return redirect()
                ->route('student.dashboard')
                ->with('error', 'This exam is no longer open.');
        }

        if ($attempt->status === 'completed') {
            return redirect()->route(
                'student.exam.result',
                $attempt->id
            );
        }

        if ($attempt->status === 'ongoing') {
            $endTime = $attempt->exam->effectiveDeadline($attempt->started_at);

            if (now()->gte($endTime)) {
                $attempt->update(['status' => 'timed_out']);

                return redirect()
                    ->route('student.dashboard')
                    ->with('error', 'Your exam time has expired.');
            }
        }

        $endTime = $attempt->exam->effectiveDeadline($attempt->started_at);

        $remainingSeconds = max(
            0,
            now()->diffInSeconds(
                $endTime,
                false
            )
        );

        $attempt->load(
            'exam.questions.options'
        );

        return view(
            'student.exam.take',
            compact('attempt', 'remainingSeconds')
        );
    }

    /*
    Submit Exam
    */
    public function submitExam(Request $request, ExamAttempt $attempt)
    {
        $this->authorizeAttempt($attempt);

        if (! $attempt->exam->isWithinAvailabilityWindow()) {
            if ($attempt->status === 'ongoing') {
                $attempt->update(['status' => 'timed_out']);
            }

            return redirect()
                ->route('student.dashboard')
                ->with('error', 'The exam deadline has passed.');
        }

        if ($attempt->status === 'completed') {
            return redirect()
                ->route(
                    'student.exam.result',
                    $attempt->id
                )
                ->with(
                    'error',
                    'This exam has already been submitted.'
                );
        }

        $attempt->load(
            'exam.questions.options'
        );

        // Check the effective deadline before saving anything.
        $deadline = $attempt->exam->effectiveDeadline($attempt->started_at);

        if (
            now()->greaterThan($deadline)
        ) {
            $attempt->update(['status' => 'timed_out']);

            return redirect()
                ->route('student.dashboard')
                ->with(
                    'error',
                    'The submission could not be accepted because the exam time has expired.'
                );
        }

        $score = 0;
        $totalScore = 0;

        foreach (
            $attempt->exam->questions as $question
        ) {

            $studentAnswer =
                $request->answers[$question->id]
                ?? '';

            $isCorrect = false;

            /*
            MCQ
            */

            if (
                $question->question_type
                == 'mcq'
            ) {

                $isCorrect =
                    trim($studentAnswer)
                    ==
                    trim(
                        $question->correct_answer
                    );
            }

            /*
            TRUE / FALSE
            */ elseif (
                $question->question_type
                == 'tf'
            ) {

                $isCorrect =
                    strtolower(
                        trim($studentAnswer)
                    )
                    ==
                    strtolower(
                        trim(
                            $question->correct_answer
                        )
                    );
            }

            /*
            IDENTIFICATION
            */ elseif (
                $question->question_type
                ==
                'identification'
            ) {

                $isCorrect =
                    strtolower(
                        trim($studentAnswer)
                    )
                    ==
                    strtolower(
                        trim(
                            $question->correct_answer
                        )
                    );
            }

            /*
            Save Answer
            */

            Answer::create([

                'attempt_id' => $attempt->id,

                'question_id' => $question->id,

                'answer_text' => $studentAnswer,

                'is_correct' => $isCorrect,

            ]);

            $totalScore +=
                $question->points;

            if ($isCorrect) {

                $score +=
                    $question->points;
            }
        }

        /*
        Update Attempt
        */

        $attempt->update([

            'score' => $score,

            'total_score' => $totalScore,

            'submitted_at' => now(),

            'status' => 'completed',

        ]);

        // 1. Analytics
        app(StudentAnalyticsService::class)
            ->updateStudentAnalytics(
                $attempt->student_id
            );

        // 2. Ranking
        app(StudentRankingService::class)
            ->updateRankings();

        // 3. Recommendation
        app(RecommendationService::class)
            ->generate($attempt);

        // 4. Performance Prediction
        app(PerformancePredictionService::class)
            ->predict($attempt->student_id);

        // 5. Academic Readiness
        app(AcademicReadinessService::class)
            ->evaluate($attempt->student_id);

        app(ActivityLogger::class)->log(
            'exam.submitted',
            'Student submitted an examination.',
            $attempt,
            ['exam_id' => $attempt->exam_id]
        );

        return redirect()->route(
            'student.exam.result',
            $attempt->id
        );
    }

    /*
    Result Page
    */
    public function result(ExamAttempt $attempt)
    {
        $this->authorizeAttempt($attempt);

        if ($attempt->status !== 'completed') {
            return redirect()
                ->route(
                    'student.exam.take',
                    $attempt->id
                )
                ->with(
                    'error',
                    'Complete the exam before viewing the result.'
                );
        }

        $attempt->load([
            'exam.subject',
            'exam.questions.options',
            'answers.question.options',
        ]);

        return view(
            'student.exam.result',
            compact('attempt')
        );
    }

    private function authorizeAttempt(ExamAttempt $attempt): void
    {
        if ($attempt->student_id !== auth()->id()) {
            abort(403, 'You are not authorized to access this exam attempt.');
        }

        $this->authorizeExam($attempt->exam);
    }

    private function authorizeExam(Exam $exam): void
    {
        abort_unless(
            $exam->isAvailableToStudent(auth()->user()),
            403,
            'You are not enrolled in this subject.'
        );
    }
}
