<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\Question;
use App\Models\QuestionOption;

class QuestionController extends Controller
{
    public function edit(Question $question)
    {
        $question->load('exam', 'options');

        $this->authorizeExam($question->exam);

        return view(
            'teacher.questions.edit',
            compact('question')
        );
    }
    private function authorizeExam(Exam $exam): void
    {
        if ($exam->created_by !== auth()->id()) {
            abort(403, 'You are not authorized to manage this exam.');
        }
    }

    public function update(Request $request, Question $question)
    {
        $question->load('exam', 'options');

        $this->authorizeExam($question->exam);

        $rules = [
            'question_text' => 'required|string|max:1000',
            'topic' => 'nullable|string|max:255',
            'correct_answer' => 'required|string|max:500',
            'points' => 'required|integer|min:1|max:100',
        ];

        if ($question->question_type === 'mcq') {
            $rules = array_merge($rules, [
                'choice_a' => 'required|string|max:500',
                'choice_b' => 'required|string|max:500',
                'choice_c' => 'required|string|max:500',
                'choice_d' => 'required|string|max:500',
            ]);
        }

        $validated = $request->validate($rules);

        $question->update([
            'question_text' => $validated['question_text'],
            'topic' => $validated['topic'] ?? null,
            'correct_answer' => $validated['correct_answer'],
            'points' => $validated['points'],
        ]);

        if ($question->question_type === 'mcq') {

            $texts = [
                $request->choice_a,
                $request->choice_b,
                $request->choice_c,
                $request->choice_d,
            ];

            foreach ($question->options as $index => $option) {

                if (!isset($texts[$index])) {
                    continue;
                }

                $option->update([
                    'option_text' => $texts[$index],
                    'is_correct' =>
                    $request->correct_answer === $texts[$index],
                ]);
            }
        }

        return back()->with(
            'success',
            'Question updated successfully.'
        );
    }

    public function destroy(Question $question)
    {
        $question->load('exam');

        $this->authorizeExam($question->exam);

        $question->delete();

        return back()->with(
            'success',
            'Question deleted successfully.'
        );
    }

    public function index(Exam $exam)
    {
        $this->authorizeExam($exam);

        $questions = $exam->questions()
            ->with('options')
            ->latest()
            ->get();

        $totalQuestions = $questions->count();
        $totalPoints = $questions->sum('points');

        return view(
            'teacher.questions.index',
            compact(
                'exam',
                'questions',
                'totalQuestions',
                'totalPoints'
            )
        );
    }

    public function create(Exam $exam)
    {
        $this->authorizeExam($exam);

        return view(
            'teacher.questions.create',
            compact('exam')
        );
    }

    public function store(Request $request, Exam $exam)
    {
        $this->authorizeExam($exam);

        $rules = [
            'question_type' => 'required|in:mcq,tf,identification',
            'question_text' => 'required|string|max:1000',
            'topic' => 'nullable|string|max:255',
            'points' => 'required|integer|min:1|max:100',
        ];

        if ($request->question_type === 'mcq') {

            $rules['options'] = 'required|array|size:4';
            $rules['options.*'] = 'required|string|max:500';
            $rules['correct_option'] = 'required|integer|between:0,3';
        } elseif ($request->question_type === 'tf') {

            $rules['correct_answer'] = 'required|in:True,False';
        } elseif ($request->question_type === 'identification') {

            $rules['identification_answer'] = 'required|string|max:500';
        }

        $validated = $request->validate($rules);

        /*
    |--------------------------------------------------------------------------
    | Determine correct answer
    |--------------------------------------------------------------------------
    */

        if ($validated['question_type'] === 'mcq') {

            $correctAnswer =
                $validated['options'][$validated['correct_option']];
        } elseif ($validated['question_type'] === 'tf') {

            $correctAnswer =
                $validated['correct_answer'];
        } else {

            $correctAnswer =
                $validated['identification_answer'];
        }

        /*
    |--------------------------------------------------------------------------
    | Save Question
    |--------------------------------------------------------------------------
    */

        $question = Question::create([
            'exam_id' => $exam->id,
            'question_type' => $validated['question_type'],
            'question_text' => $validated['question_text'],
            'topic' => $validated['topic'] ?? null,
            'correct_answer' => $correctAnswer,
            'points' => $validated['points'],
        ]);

        /*
    |--------------------------------------------------------------------------
    | Save MCQ Options
    |--------------------------------------------------------------------------
    */

        if ($validated['question_type'] === 'mcq') {

            foreach (
                $validated['options']
                as $index => $option
            ) {

                QuestionOption::create([
                    'question_id' => $question->id,
                    'option_text' => $option,
                    'is_correct' =>
                    $index ===
                        (int) $validated['correct_option'],
                ]);
            }
        }

        return redirect()
            ->route(
                'teacher.exams.questions',
                $exam->id
            )
            ->with(
                'success',
                'Question added successfully.'
            );
    }
}
