<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    /**
     * Show classes joined by the current student.
     */
    public function index()
    {
        $classes = auth()->user()
            ->enrolledClasses()
            ->with([
                'subject',
                'teacher',
            ])
            ->withCount([
                'exams as published_exams_count' => function ($query) {
                    $query->where('status', 'published');
                }
            ])
            ->latest('class_enrollments.created_at')
            ->get();

        return view(
            'student.classes.index',
            compact('classes')
        );
    }

    /**
     * Join a class using its class code.
     */
    public function join(Request $request)
    {
        $validated = $request->validate([
            'class_code' => [
                'required',
                'string',
                'max:10',
            ],
        ]);

        $code = strtoupper(
            trim($validated['class_code'])
        );

        $class = SchoolClass::where(
            'class_code',
            $code
        )->first();

        if (!$class) {
            return back()
                ->withInput()
                ->withErrors([
                    'class_code' =>
                    'The class code you entered is invalid.',
                ]);
        }

        $student = auth()->user();

        if (
            $student->enrolledClasses()
            ->where('classes.id', $class->id)
            ->exists()
        ) {
            return back()->with(
                'info',
                'You are already enrolled in this class.'
            );
        }

        $student->enrolledClasses()->attach(
            $class->id,
            [
                'joined_at' => now(),
            ]
        );

        return redirect()
            ->route('student.classes.show', $class)
            ->with(
                'success',
                'You joined the class successfully.'
            );
    }

    /**
     * Open a joined class.
     */
    public function show(SchoolClass $class)
    {
        $student = auth()->user();

        $isEnrolled = $student
            ->enrolledClasses()
            ->where(
                'classes.id',
                $class->id
            )
            ->exists();

        abort_unless($isEnrolled, 403);

        $class->load([
            'subject',
            'teacher',
            'exams' => function ($query) {
                $query
                    ->where('status', 'published')
                    ->latest();
            },
        ]);

        return view(
            'student.classes.show',
            compact('class')
        );
    }
}
