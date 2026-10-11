<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index(Request $request)
    {
        $classes = SchoolClass::query()
            ->with(['teacher', 'subject'])
            ->withCount(['students', 'exams'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('class_code', 'like', "%{$search}%")
                        ->orWhereHas('teacher', function ($teacher) use ($search) {
                            $teacher->where('fname', 'like', "%{$search}%")
                                ->orWhere('lname', 'like', "%{$search}%");
                        })
                        ->orWhereHas('subject', function ($subject) use ($search) {
                            $subject->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.classes.index', compact('classes'));
    }

    public function show(SchoolClass $class)
    {
        $class->load(['teacher', 'subject']);

        $students = $class->students()
            ->orderBy('lname')
            ->paginate(15);

        $exams = $class->exams()
            ->withCount('attempts')
            ->latest()
            ->get();

        return view('admin.classes.show', compact(
            'class',
            'students',
            'exams'
        ));
    }
}
