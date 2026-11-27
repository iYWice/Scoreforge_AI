<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Exam;
use App\Models\SchoolClass;
use App\Models\ExamAttempt;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'users' => User::count(),
            'teachers' => User::where('role', 'teacher')->count(),
            'students' => User::where('role', 'student')->count(),
            'classes' => SchoolClass::count(),
            'exams' => Exam::count(),
            'attempts' => ExamAttempt::count(),
        ];

        $recentUsers = User::latest()->take(5)->get();

        $recentExams = Exam::with('teacher')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'recentUsers',
            'recentExams'
        ));
    }
}
