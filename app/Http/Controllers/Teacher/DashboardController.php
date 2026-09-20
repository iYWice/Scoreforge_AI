<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Question;
use App\Models\ExamAttempt;

class DashboardController extends Controller
{
    public function index()
    {
        $teacherId = auth()->id();

        // Total exams created by this teacher
        $totalExams = Exam::where('created_by', $teacherId)->count();

        // Total questions belonging to this teacher's exams
        $totalQuestions = Question::whereHas('exam', function ($query) use ($teacherId) {
            $query->where('created_by', $teacherId);
        })->count();

        // Total student attempts on this teacher's exams
        $totalAttempts = ExamAttempt::whereHas('exam', function ($query) use ($teacherId) {
            $query->where('created_by', $teacherId);
        })->count();

        // Average score of completed attempts
        $averageScore = ExamAttempt::whereHas('exam', function ($query) use ($teacherId) {
            $query->where('created_by', $teacherId);
        })
            ->where('status', 'completed')
            ->avg('score');

        return view('teacher.dashboard', compact(
            'totalExams',
            'totalQuestions',
            'totalAttempts',
            'averageScore'
        ));
    }
}
