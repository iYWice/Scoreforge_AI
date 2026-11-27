<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\QuestionGenerationSession;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Smalot\PdfParser\Parser;
use Throwable;
use App\Models\SystemSetting;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Support\Facades\DB;

class AIQuestionBuilderController extends Controller
{
    /**
     * Show AI Question Builder.
     */
    public function create(Exam $exam)
    {
        abort_if(
            $exam->created_by !== auth()->id(),
            403
        );
        abort_if(
            SystemSetting::getValue('ai_question_builder', '1') !== '1',
            403,
            'AI Question Builder is currently disabled by the administrator.'
        );

        $exam->load('subject');

        return view(
            'teacher.ai-questions.create',
            compact('exam')
        );
    }

    /**
     * Upload PDF, extract text,
     * generate questions, and save draft.
     */
    public function generate(
        Request $request,
        Exam $exam,
        GeminiService $geminiService
    ) {
        abort_if(
            $exam->created_by !== auth()->id(),
            403
        );
        abort_if(
            SystemSetting::getValue('ai_question_builder', '1') !== '1',
            403,
            'AI Question Builder is currently disabled by the administrator.'
        );

        /*
        |--------------------------------------------------------------------------
        | Validate
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'source_file' => [
                'required',
                'file',
                'mimes:pdf',
                'max:10240',
            ],

            'question_count' => [
                'required',
                'integer',
                'min:1',
                'max:50',
            ],

            'question_types' => [
                'required',
                'array',
                'min:1',
            ],

            'question_types.*' => [
                'required',
                'in:multiple_choice,true_false,identification',
            ],

            'difficulty' => [
                'nullable',
                'in:easy,medium,hard',
            ],
        ]);

        $uploadedFile = $request->file('source_file');

        /*
        |--------------------------------------------------------------------------
        | Save uploaded PDF
        |--------------------------------------------------------------------------
        */

        $storedPath = $uploadedFile->store(
            'question-builder',
            'local'
        );

        try {

            /*
            |--------------------------------------------------------------------------
            | Extract PDF text
            |--------------------------------------------------------------------------
            */

            $fullPath = Storage::disk('local')
                ->path($storedPath);

            $parser = new Parser();

            $pdf = $parser->parseFile($fullPath);

            $sourceText = trim(
                $pdf->getText()
            );

            if ($sourceText === '') {
                throw new RuntimeException(
                    'No readable text could be extracted from the PDF.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Generate questions using Gemini
            |--------------------------------------------------------------------------
            */

            $questions = $geminiService
                ->generateQuestions(
                    $sourceText,
                    (int) $validated['question_count'],
                    $validated['question_types'],
                    $validated['difficulty'] ?? null
                );

            /*
            |--------------------------------------------------------------------------
            | Save generation as draft
            |--------------------------------------------------------------------------
            */

            $session = QuestionGenerationSession::create([
                'user_id' => auth()->id(),

                'exam_id' => $exam->id,

                'source_name' =>
                $uploadedFile->getClientOriginalName(),

                'source_file' => $storedPath,

                'purpose' => 'exam',

                'question_count' =>
                count($questions),

                'question_types' =>
                $validated['question_types'],

                'difficulty' =>
                $validated['difficulty'] ?? null,

                'generated_questions' =>
                $questions,

                'status' => 'draft',
            ]);
            app(\App\Services\ActivityLogger::class)->log(
                'questions.ai_generated',
                'Teacher generated questions with AI.',
                $session,
                [
                    'exam_id' => $exam->id,
                    'question_count' => count($questions),
                ]
            );

            /*
            |--------------------------------------------------------------------------
            | Go to draft review
            |--------------------------------------------------------------------------
            */

            return redirect()->route(
                'teacher.ai-questions.review',
                [
                    'exam' => $exam,
                    'session' => $session,
                ]
            );
        } catch (Throwable $exception) {

            /*
            |--------------------------------------------------------------------------
            | Remove PDF when generation fails
            |--------------------------------------------------------------------------
            */

            Storage::disk('local')
                ->delete($storedPath);

            report($exception);

            return back()
                ->withInput()
                ->withErrors([
                    'source_file' =>
                    $exception->getMessage(),
                ]);
        }
    }

    /**
     * Show generated draft questions.
     */
    public function review(
        Exam $exam,
        QuestionGenerationSession $session
    ) {
        abort_if(
            $exam->created_by !== auth()->id(),
            403
        );

        abort_if(
            $session->exam_id !== $exam->id &&
                !($session->purpose === 'question_bank' && $session->status === 'saved'),
            404
        );

        $exam->load('subject');

        return view(
            'teacher.ai-questions.review',
            compact(
                'exam',
                'session'
            )
        );
    }
    public function commit(
        Request $request,
        Exam $exam,
        QuestionGenerationSession $session
    ) {
        abort_if($exam->created_by !== auth()->id(), 403);
        abort_if($session->user_id !== auth()->id(), 403);

        abort_if(
            $session->exam_id !== $exam->id &&
                !($session->purpose === 'question_bank' && $session->status === 'saved'),
            404
        );

        abort_if(
            $session->status === 'used',
            409,
            'These questions have already been added to an exam.'
        );

        $validated = $request->validate([
            'action' => 'required|in:save,use',
            'selected_questions' => 'required|array|min:1',
            'selected_questions.*' => 'required|integer|min:0',
        ]);

        $questions = $session->generated_questions ?? [];

        $selectedQuestions = collect($validated['selected_questions'])
            ->unique()
            ->map(function (int $index) use ($questions) {
                abort_unless(
                    array_key_exists($index, $questions),
                    422,
                    'A selected question is no longer available.'
                );

                return $questions[$index];
            })
            ->values();
        if ($validated['action'] === 'save') {
            $savedSet = QuestionGenerationSession::create([
                'user_id' => auth()->id(),
                'exam_id' => null,
                'source_name' => $session->source_name,
                'source_file' => null,
                'purpose' => 'question_bank',
                'question_count' => $selectedQuestions->count(),
                'question_types' => $selectedQuestions
                    ->pluck('question_type')
                    ->unique()
                    ->values()
                    ->all(),
                'difficulty' => $session->difficulty,
                'generated_questions' => $selectedQuestions->all(),
                'status' => 'saved',
            ]);

            app(\App\Services\ActivityLogger::class)->log(
                'questions.saved_to_bank',
                'Teacher saved generated questions to the question bank.',
                $savedSet,
                ['question_count' => $selectedQuestions->count()]
            );

            return back()->with(
                'success',
                'Selected questions were saved to your question bank.'
            );
        }
        DB::transaction(function () use ($exam, $selectedQuestions, $session): void {
            foreach ($selectedQuestions as $questionData) {
                $type = match ($questionData['question_type'] ?? null) {
                    'multiple_choice' => 'mcq',
                    'true_false' => 'tf',
                    'identification' => 'identification',
                    default => abort(422, 'A generated question has an unsupported type.'),
                };

                $question = Question::create([
                    'exam_id' => $exam->id,
                    'question_type' => $type,
                    'topic' => $questionData['topic'] ?? null,
                    'question_text' => $questionData['question_text'],
                    'correct_answer' => $questionData['correct_answer'],
                    'points' => max(1, min((int) ($questionData['points'] ?? 1), 100)),
                ]);

                if ($type === 'mcq') {
                    foreach ($questionData['options'] ?? [] as $option) {
                        QuestionOption::create([
                            'question_id' => $question->id,
                            'option_text' => $option,
                            'is_correct' => $option === $questionData['correct_answer'],
                        ]);
                    }
                }
            }

            if ($session->purpose !== 'question_bank') {
                $session->update(['status' => 'used']);
            }
        });

        app(\App\Services\ActivityLogger::class)->log(
            'questions.added_to_exam',
            'Teacher reused generated questions in an examination.',
            $exam,
            ['question_count' => $selectedQuestions->count()]
        );

        return redirect()
            ->route('teacher.exams.questions', $exam)
            ->with(
                'success',
                $selectedQuestions->count() . ' questions added to the examination.'
            );
    }
    public function updateGeneratedQuestion(
        Request $request,
        Exam $exam,
        QuestionGenerationSession $session,
        int $questionIndex
    ) {
        abort_if($exam->created_by !== auth()->id(), 403);
        abort_if($session->user_id !== auth()->id(), 403);
        abort_if($session->exam_id !== $exam->id, 404);
        abort_if($session->status === 'used', 409);

        $questions = $session->generated_questions ?? [];

        abort_unless(array_key_exists($questionIndex, $questions), 404);

        $question = $questions[$questionIndex];

        $validated = $request->validate([
            'question_text' => 'required|string|max:1000',
            'topic' => 'nullable|string|max:255',
            'correct_answer' => 'required|string|max:500',
            'points' => 'required|integer|min:1|max:100',
            'options' => $question['question_type'] === 'multiple_choice'
                ? 'required|array|size:4'
                : 'nullable|array',
            'options.*' => 'required|string|max:500',
        ]);

        if (
            $question['question_type'] === 'multiple_choice' &&
            !in_array($validated['correct_answer'], $validated['options'], true)
        ) {
            return back()
                ->withErrors([
                    'correct_answer' => 'The correct answer must match one of the choices.',
                ])
                ->withInput();
        }

        $question['question_text'] = $validated['question_text'];
        $question['topic'] = $validated['topic'] ?? null;
        $question['correct_answer'] = $validated['correct_answer'];
        $question['points'] = (int) $validated['points'];

        if ($question['question_type'] === 'multiple_choice') {
            $question['options'] = $validated['options'];
        }

        $questions[$questionIndex] = $question;

        $session->update([
            'generated_questions' => $questions,
        ]);

        app(\App\Services\ActivityLogger::class)->log(
            'question.ai_draft_updated',
            'Teacher edited an AI-generated question.',
            $session,
            ['question_index' => $questionIndex]
        );

        return back()->with('success', 'Generated question updated.');
    }
}
