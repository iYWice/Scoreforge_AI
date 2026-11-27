<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\View\View;

/**
 * Provides summary data for the administrator dashboard.
 */
class DashboardController extends Controller
{
    /**
     * Collect platform counts and recent records for the dashboard view.
     */
    public function index(): View
    {
        // Count each account type and the main platform resources.
        $stats = [
            'users' => User::count(),
            'teachers' => User::where('role', 'teacher')->count(),
            'students' => User::where('role', 'student')->count(),
            'classes' => SchoolClass::count(),
            'exams' => Exam::count(),
            'attempts' => ExamAttempt::count(),
        ];

        // Load the five most recently created accounts.
        $recentUsers = User::latest()->take(5)->get();

        // Load recent examinations with their teacher data to avoid additional lookups.
        $recentExams = Exam::with('teacher')
            ->latest()
            ->take(5)
            ->get();

        // Pass summary and activity data to the admin dashboard template.
        return view('admin.dashboard', compact(
            'stats',
            'recentUsers',
            'recentExams'
        ));
    }
}
