<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubjectController extends Controller
{
    public function index(Request $request): View
    {
        $subjects = Subject::query()
            ->withCount(['exams', 'classSubjects'])
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = $request->string('search')->toString();
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.subjects.index', compact('subjects'));
    }

    public function store(Request $request, ActivityLogger $logger): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:subjects,name'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $subject = Subject::create($data);
        $logger->log('subject.created', 'Administrator created a subject.', $subject);

        return back()->with('success', 'Subject created successfully.');
    }

    public function update(Request $request, Subject $subject, ActivityLogger $logger): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:subjects,name,'.$subject->id],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $subject->update($data);
        $logger->log('subject.updated', 'Administrator updated a subject.', $subject);

        return back()->with('success', 'Subject updated successfully.');
    }

    public function destroy(Subject $subject, ActivityLogger $logger): RedirectResponse
    {
        $subjectName = $subject->name;

        if ($subject->exams()->exists() || $subject->classSubjects()->exists()) {
            return back()->withErrors([
                'subject' => 'Subject cannot be deleted while it is used by classes or examinations.',
            ]);
        }

        $subject->delete();
        $logger->log('subject.deleted', 'Administrator deleted subject: '.$subjectName);

        return back()->with('success', 'Subject deleted successfully.');
    }
}
