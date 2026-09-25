<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index()
    {
        $classes = SchoolClass::where('created_by', auth()->id())
            ->with('subject')
            ->withCount('students')
            ->latest()
            ->get();

        $subjects = Subject::orderBy('name')->get();

        return view('teacher.classes.index', compact(
            'classes',
            'subjects'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'subject_id' => ['required', 'exists:subjects,id'],
        ]);

        SchoolClass::create([
            'name' => $validated['name'],
            'subject_id' => $validated['subject_id'],
            'created_by' => auth()->id(),
            'class_code' => SchoolClass::generateClassCode(),
        ]);

        return redirect()
            ->route('teacher.classes.index')
            ->with('success', 'Class created successfully.');
    }

    public function show(SchoolClass $class)
    {
        abort_if(
            $class->created_by !== auth()->id(),
            403
        );

        $class->load([
            'subject',
            'students',
            'exams' => fn($query) => $query->latest(),
        ]);

        return view(
            'teacher.classes.show',
            compact('class')
        );
    }

    public function update(
        Request $request,
        SchoolClass $class
    ) {
        abort_if(
            $class->created_by !== auth()->id(),
            403
        );

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'subject_id' => ['required', 'exists:subjects,id'],
        ]);

        $class->update($validated);

        return back()->with(
            'success',
            'Class updated successfully.'
        );
    }
}
//<?php

// namespace App\Http\Controllers\Teacher;

// use App\Http\Controllers\Controller;
// use App\Models\SchoolClass;
// use App\Models\Subject;
// use Illuminate\Http\Request;

// class ClassController extends Controller
// {
//     public function index()
//     {
//         $classes = SchoolClass::where(
//             'created_by',
//             auth()->id()
//         )
//             ->with('subject')
//             ->withCount('students')
//             ->latest()
//             ->get();

//         $subjects = Subject::orderBy('name')->get();

//         return view('teacher.classes.index', compact(
//             'classes',
//             'subjects'
//         ));
//     }

//     public function store(Request $request)
//     {
//         $validated = $request->validate([
//             'name' => [
//                 'required',
//                 'string',
//                 'max:255',
//             ],
//             'subject_id' => [
//                 'required',
//                 'exists:subjects,id',
//             ],
//         ]);

//         SchoolClass::create([
//             'name' => $validated['name'],
//             'subject_id' => $validated['subject_id'],
//             'created_by' => auth()->id(),
//             'class_code' =>
//             SchoolClass::generateClassCode(),
//         ]);

//         return redirect()
//             ->route('teacher.classes.index')
//             ->with(
//                 'success',
//                 'Class created successfully.'
//             );
//     }

//     public function show(SchoolClass $class)
//     {
//         abort_if(
//             $class->created_by !== auth()->id(),
//             403
//         );

//         $class->load([
//             'subject',
//             'students',
//             'exams' => function ($query) {
//                 $query->latest();
//             },
//         ]);

//         return view(
//             'teacher.classes.show',
//             compact('class')
//         );
//     }

//     public function update(
//         Request $request,
//         SchoolClass $class
//     ) {
//         abort_if(
//             $class->created_by !== auth()->id(),
//             403
//         );

//         $validated = $request->validate([
//             'name' => [
//                 'required',
//                 'string',
//                 'max:255',
//             ],
//             'subject_id' => [
//                 'required',
//                 'exists:subjects,id',
//             ],
//         ]);

//         $class->update($validated);

//         return back()->with(
//             'success',
//             'Class updated successfully.'
//         );
//     }
// }
