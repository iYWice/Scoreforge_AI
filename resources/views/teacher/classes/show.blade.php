@extends('layouts.app')

@section('title', $class->name)
@section('eyebrow', 'Class Workspace')

@section('content')

<div class="mx-auto max-w-7xl">
    <div class="mx-auto max-w-7xl px-6 py-8">

        {{-- Back --}}
        <a
            href="{{ route('teacher.classes.index') }}"
            class="text-sm font-medium text-slate-500 hover:text-indigo-600">
            ← Classes
        </a>

        {{-- Class Header --}}
        <div class="mt-5 flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
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

            <div class="rounded-xl border border-indigo-100 bg-indigo-50 px-5 py-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-indigo-500">
                    Class Code
                </p>

                <div class="mt-1 flex items-center gap-4">
                    <span
                        id="class-code"
                        class="font-mono text-xl font-bold tracking-[0.18em] text-indigo-950">
                        {{ $class->class_code }}
                    </span>

                    <button
                        type="button"
                        onclick="navigator.clipboard.writeText('{{ $class->class_code }}')"
                        class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                        Copy
                    </button>
                </div>
            </div>
        </div>

        {{-- Stats --}}
        <div class="mt-8 grid gap-4 sm:grid-cols-2">
            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <p class="text-sm text-slate-500">
                    Students
                </p>

                <p class="mt-2 text-3xl font-semibold text-slate-950">
                    {{ $class->students->count() }}
                </p>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5">
                <p class="text-sm text-slate-500">
                    Examinations
                </p>

                <p class="mt-2 text-3xl font-semibold text-slate-950">
                    {{ $class->exams->count() }}
                </p>
            </div>
        </div>

        <div class="mt-8 grid gap-8 lg:grid-cols-[1fr_1.2fr]">

            {{-- Students --}}
            <section>
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-950">
                            Students
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            Students who joined using the class code.
                        </p>
                    </div>
                </div>

                <div class="mt-4 overflow-hidden rounded-xl border border-slate-200 bg-white">
                    @forelse($class->students as $student)

                    <div class="flex items-center gap-3 border-b border-slate-100 px-5 py-4 last:border-0">
                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-100 text-sm font-semibold text-slate-600">
                            {{ strtoupper(substr($student->fname, 0, 1)) }}
                        </div>

                        <div>
                            <p class="text-sm font-semibold text-slate-900">
                                {{ $student->fname }}
                                {{ $student->lname }}
                            </p>

                            <p class="text-xs text-slate-500">
                                {{ $student->email }}
                            </p>
                        </div>
                    </div>

                    @empty

                    <div class="px-6 py-12 text-center">
                        <p class="text-sm font-medium text-slate-700">
                            No students yet
                        </p>

                        <p class="mt-1 text-sm text-slate-500">
                            Share the class code
                            <strong>{{ $class->class_code }}</strong>
                            with your students.
                        </p>
                    </div>

                    @endforelse
                </div>
            </section>

            {{-- Exams --}}
            <section>
                <div class="flex items-center justify-between">
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
                        class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">
                        + Create Exam
                    </a>
                </div>

                <div class="mt-4 overflow-hidden rounded-xl border border-slate-200 bg-white">
                    @forelse($class->exams as $exam)

                    <a
                        href="{{ route('teacher.exams.show', $exam) }}"
                        class="flex items-center justify-between border-b border-slate-100 px-5 py-4 transition last:border-0 hover:bg-slate-50">
                        <div>
                            <p class="text-sm font-semibold text-slate-900">
                                {{ $exam->title }}
                            </p>

                            <p class="mt-1 font-mono text-xs text-slate-400">
                                {{ $exam->exam_code }}
                            </p>
                        </div>

                        <span class="rounded-md bg-slate-100 px-2.5 py-1 text-xs font-semibold capitalize text-slate-600">
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