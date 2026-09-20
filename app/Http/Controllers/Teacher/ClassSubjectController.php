<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Http\Request;

class ClassSubjectController extends Controller
{
    public function index()
    {
        $classes = SchoolClass::latest()->get();
        $subjects = Subject::latest()->get();

        return view('teacher.class-subjects.index', compact(
            'classes',
            'subjects'
        ));
    }


    // CREATE CLASS
    public function storeClass(Request $request)
    {
        $validated = $request->validateWithBag(
            'classCreation',
            [
                'name' => 'required|string|max:255',
            ]
        );

        SchoolClass::create([
            'name' => $validated['name'],
        ]);

        return redirect()
            ->route('teacher.class-subjects.index')
            ->with('success', 'Class created successfully.');
    }


    // CREATE SUBJECT
    public function storeSubject(Request $request)
    {
        $validated = $request->validateWithBag(
            'subjectCreation',
            [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
            ]
        );

        Subject::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()
            ->route('teacher.class-subjects.index')
            ->with('success', 'Subject created successfully.');
    }


    // DELETE CLASS
    public function destroyClass($id)
    {
        $class = SchoolClass::findOrFail($id);

        if ($class->exams()->exists()) {

            return back()->with(
                'error',
                'This class cannot be deleted because it is currently used by one or more exams.'
            );
        }

        $class->delete();

        return back()->with(
            'success',
            'Class deleted successfully.'
        );
    }


    // DELETE SUBJECT
    public function destroySubject($id)
    {
        $subject = Subject::findOrFail($id);

        if ($subject->exams()->exists()) {

            return back()->with(
                'error',
                'This subject cannot be deleted because it is currently used by one or more exams.'
            );
        }

        $subject->delete();

        return back()->with(
            'success',
            'Subject deleted successfully.'
        );
    }
}
