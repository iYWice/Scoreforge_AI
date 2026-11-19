@extends('layouts.app')

@section('title', $class->name)
@section('eyebrow', 'Class Workspace')

@section('content')
<div class="mx-auto max-w-7xl space-y-8 px-4 py-8 sm:px-6 lg:px-8">

    {{-- Back --}}
    <a
        href="{{ route('teacher.classes.index') }}"
        class="text-sm font-medium text-slate-500 hover:text-indigo-600">
        ← Classes
    </a>

    {{-- Class header --}}
    <header class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-600">
                {{ $class->subject?->name ?? 'No Subject' }}
            </p>

            <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950">
                {{ $class->name }}
            </h1>

            <p class="mt-2 text-sm text-slate-500">
                Manage students and examinations assigned to this class.
            </p>
        </div>

        <div class="w-full rounded-xl border border-indigo-100 bg-indigo-50 px-5 py-4 sm:w-auto">
            <p class="text-xs font-semibold uppercase tracking-wider text-indigo-500">
                Class Code
            </p>

            <div class="mt-1 flex items-center justify-between gap-4 sm:justify-start">
                <span
                    id="class-code"
                    class="font-mono text-xl font-bold tracking-[0.18em] text-indigo-950">
                    {{ $class->class_code }}
                </span>

                <button
                    type="button"
                    onclick="navigator.clipboard.writeText(@js($class->class_code))"
                    class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                    Copy
                </button>
            </div>
        </div>
    </header>

    {{-- Stats --}}
    <section class="grid gap-4 sm:grid-cols-2">
        <div class="rounded-xl border border-slate-200 bg-white p-5">
            <p class="text-sm text-slate-500">Students</p>
            <p class="mt-2 text-3xl font-semibold text-slate-950">
                {{ $class->students->count() }}
            </p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-5">
            <p class="text-sm text-slate-500">Examinations</p>
            <p class="mt-2 text-3xl font-semibold text-slate-950">
                {{ $class->exams->count() }}
            </p>
        </div>
    </section>

    <div class="grid gap-8 lg:grid-cols-[1fr_1.2fr]">

        {{-- Students --}}
        <section>
            <div>
                <h2 class="text-lg font-semibold text-slate-950">Students</h2>
                <p class="mt-1 text-sm text-slate-500">
                    Students who joined using the class code.
                </p>
            </div>

            <div class="mt-4 overflow-hidden rounded-xl border border-slate-200 bg-white">
                @forelse($class->students as $student)
                    <article class="flex flex-col gap-4 border-b border-slate-100 px-5 py-4 last:border-0 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex min-w-0 items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-100 text-sm font-semibold text-slate-600">
                                {{ strtoupper(substr($student->fname ?? '', 0, 1)) }}
                            </div>

                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-slate-900">
                                    {{ $student->fname }} {{ $student->lname }}
                                </p>
                                <p class="truncate text-xs text-slate-500">
                                    {{ $student->email }}
                                </p>
                                <p class="truncate text-xs font-semibold text-slate-600">
                                    Student ID: {{ $student->student_id ?: 'Not provided' }}
                                </p>
                            </div>
                            
                        </div>

                        <div class="flex flex-wrap items-center gap-4 pl-[3.25rem] sm:pl-0">
                            <a
                                href="{{ route('teacher.students.performance', $student) }}"
                                class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                                View performance →
                            </a>

                            <form
                                method="POST"
                                action="{{ route('teacher.classes.students.remove', [$class, $student]) }}"
                                onsubmit="return confirm('Remove this student from the class and its subject rosters? Their exam history will be kept.');">
                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="text-xs font-semibold text-rose-600 hover:text-rose-800">
                                    Remove from class
                                </button>
                            </form>
                        </div>
                    </article>
                @empty
                    <div class="px-6 py-12 text-center">
                        <p class="text-sm font-medium text-slate-700">No students yet</p>
                        <p class="mt-1 text-sm text-slate-500">
                            Share the class code
                            <strong class="font-mono">{{ $class->class_code }}</strong>
                            with your students.
                        </p>
                    </div>
                @endforelse
            </div>
        </section>

        {{-- Examinations --}}
        <section>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-950">
                        Class Examinations
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Examinations assigned to this class.
                    </p>
                </div>

                <a
                    href="{{ route('teacher.exams.index') }}"
                    class="inline-flex w-fit items-center rounded-lg bg-indigo-600 px-3 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700">
                    + Create Exam
                </a>
            </div>

            <div class="mt-4 overflow-hidden rounded-xl border border-slate-200 bg-white">
                @forelse($class->exams as $exam)
                    <a
                        href="{{ route('teacher.exams.show', $exam) }}"
                        class="flex items-center justify-between gap-4 border-b border-slate-100 px-5 py-4 transition last:border-0 hover:bg-slate-50">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-slate-900">
                                {{ $exam->title }}
                            </p>
                            <p class="mt-1 font-mono text-xs text-slate-400">
                                {{ $exam->exam_code }}
                            </p>
                        </div>

                        <span class="shrink-0 rounded-md bg-slate-100 px-2.5 py-1 text-xs font-semibold capitalize text-slate-600">
                            {{ $exam->status }}
                        </span>
                    </a>
                @empty
                    <div class="px-6 py-12 text-center">
                        <p class="text-sm font-medium text-slate-700">
                            No examinations assigned
                        </p>
                        <p class="mt-1 text-sm text-slate-500">
                            Create an examination and assign it to this class.
                        </p>
                    </div>
                @endforelse
            </div>
        </section>

    </div>
</div>
@endsection