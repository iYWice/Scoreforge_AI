@extends('layouts.app')

@section('title', 'Subjects')
@section('eyebrow', 'Teacher Workspace')

@section('content')
<div class="space-y-6">
    <header>
        <h1 class="text-2xl font-bold text-slate-950">Subjects</h1>
        <p class="mt-1 text-sm text-slate-500">
            View your subject offerings and the students enrolled in each one.
        </p>
    </header>

    @forelse ($offerings as $offering)
        <article class="overflow-hidden rounded-xl border border-slate-200 bg-white">
            <div class="flex flex-col justify-between gap-4 border-b border-slate-100 p-5 sm:flex-row sm:items-start">
                <div>
                    <h2 class="text-lg font-bold text-slate-950">
                        {{ $offering->subject->name }}
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Class: {{ $offering->schoolClass?->name ?? 'Independent subject' }}
                    </p>
                </div>

                @if ($offering->schoolClass)
                <a
                    href="{{ route('teacher.classes.show', $offering->schoolClass) }}"
                    class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">
                    Manage class →
                </a>
                @else
                <span class="text-sm font-semibold text-slate-500">
                    Independent subject
                </span>
                @endif
            </div>

            <div class="grid gap-4 bg-slate-50 p-5 sm:grid-cols-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                        EDP Code
                    </p>
                    <p class="mt-1 font-mono text-sm font-semibold text-slate-800">
                        {{ $offering->edp_code }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                        Schedule
                    </p>
                    <p class="mt-1 text-sm font-semibold text-slate-800">
                        {{ $offering->schedule }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                        Students
                    </p>
                    <p class="mt-1 text-sm font-semibold text-slate-800">
                        {{ $offering->students_count }}
                    </p>
                </div>
            </div>

            <div class="p-5">
                <h3 class="mb-3 text-sm font-bold text-slate-800">
                    Enrolled students
                </h3>

                <div class="divide-y divide-slate-100">
                    @forelse ($offering->students as $student)
                        <div class="flex flex-col gap-1 py-3 first:pt-0 last:pb-0 sm:flex-row sm:items-center sm:justify-between">
                            <span class="text-sm font-semibold text-slate-800">
                                {{ $student->fname }} {{ $student->lname }}
                            </span>
                            <span class="text-sm text-slate-500">
                                {{ $student->email }}
                            </span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">
                            No students are assigned to this subject yet.
                        </p>
                    @endforelse
                </div>
            </div>
        </article>
    @empty
        <div class="rounded-xl border border-dashed border-slate-300 bg-white p-10 text-center">
            <h2 class="font-semibold text-slate-800">No subject offerings yet</h2>
            <p class="mt-1 text-sm text-slate-500">
                Open one of your classes to create a subject and assign its students.
            </p>
            <a
                href="{{ route('teacher.classes.index') }}"
                class="mt-4 inline-flex rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">
                View classes
            </a>
        </div>
    @endforelse
</div>
@endsection