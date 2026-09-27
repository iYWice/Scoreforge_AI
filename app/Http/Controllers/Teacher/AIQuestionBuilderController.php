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
            $session->user_id !== auth()->id(),
            403
        );

        abort_if(
            $session->exam_id !== $exam->id,
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
}
