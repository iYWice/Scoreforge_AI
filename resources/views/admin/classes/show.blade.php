@extends('layouts.app')

@section('title', 'Class Details | AI-Q')
@section('page-title', 'Class Details')

@section('content')
<div class="space-y-6">

    <a href="{{ route('admin.classes.index') }}"
        class="text-sm font-medium text-indigo-600">
        ← Back to Classes
    </a>

    <div class="rounded-xl border border-slate-200 bg-white p-6">
        <h1 class="text-2xl font-bold text-slate-900">
            {{ $class->name }}
        </h1>

        <div class="mt-4 grid gap-4 text-sm sm:grid-cols-3">
            <div>
                <p class="text-slate-500">Class Code</p>
                <p class="font-semibold">{{ $class->class_code }}</p>
            </div>
            <div>
                <p class="text-slate-500">Subject</p>
                <p class="font-semibold">
                    {{ $class->subject?->name ?? 'Unassigned' }}
                </p>
            </div>
            <div>
                <p class="text-slate-500">Teacher</p>
                <p class="font-semibold">
                    {{ $class->teacher?->fname }}
                    {{ $class->teacher?->lname }}
                </p>
            </div>
        </div>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white p-6">
        <h2 class="mb-4 text-lg font-bold">Enrolled Students</h2>

        @forelse($students as $student)
        <div class="flex flex-wrap justify-between gap-2 border-b border-slate-100 py-3 last:border-0">
            <span class="font-medium">
                {{ $student->fname }} {{ $student->lname }}
            </span>
            <span class="text-sm text-slate-500">
                {{ $student->email }}
            </span>
        </div>
        @empty
        <p class="text-sm text-slate-500">No enrolled students.</p>
        @endforelse

        <div class="mt-4">{{ $students->links() }}</div>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white p-6">
        <h2 class="mb-4 text-lg font-bold">Class Examinations</h2>

        @forelse($exams as $exam)
        <div class="flex items-center justify-between gap-3 border-b border-slate-100 py-3 last:border-0">
            <div>
                <p class="font-medium">{{ $exam->title }}</p>
                <p class="text-xs text-slate-500">
                    {{ ucfirst($exam->status) }} ·
                    {{ $exam->attempts_count }} attempts
                </p>
            </div>
            <a href="{{ route('admin.exams.show', $exam) }}"
                class="text-sm font-semibold text-indigo-600">
                View
            </a>
        </div>
        @empty
        <p class="text-sm text-slate-500">No exams assigned to this class.</p>
        @endforelse
    </div>

</div>
@endsection