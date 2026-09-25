@extends('layouts.app')

@section('title', 'Classes')
@section('eyebrow', 'Teacher Workspace')



@section('content')
<div class="mx-auto max-w-7xl px-6 py-8"
    x-data="{ createOpen: false }">

    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-600">
                Academic Workspace
            </p>

            <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950">
                Classes
            </h1>

            <p class="mt-2 max-w-2xl text-sm text-slate-500">
                Organize students, subjects, and examinations in one workspace.
            </p>
        </div>

        <button
            type="button"
            @click="createOpen = true"
            class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">
            + Create Class
        </button>
    </div>

    {{-- Messages --}}
    @if(session('success'))
    <div class="mt-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="mt-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        <p class="font-semibold">
            Please check the information below.
        </p>

        <ul class="mt-2 list-disc pl-5">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Classes --}}
    <div class="mt-8">
        @if($classes->isEmpty())

        <div class="rounded-xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center">
            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 text-xl font-semibold text-indigo-600">
                C
            </div>

            <h2 class="mt-4 text-lg font-semibold text-slate-900">
                No classes yet
            </h2>

            <p class="mx-auto mt-2 max-w-md text-sm text-slate-500">
                Create your first class to organize students and publish examinations.
            </p>

            <button
                type="button"
                @click="createOpen = true"
                class="mt-5 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">
                Create Class
            </button>
        </div>

        @else

        <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
            @foreach($classes as $class)

            <a
                href="{{ route('teacher.classes.show', $class) }}"
                class="group rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-indigo-200 hover:shadow-md">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-indigo-600">
                            {{ $class->subject?->name ?? 'No Subject' }}
                        </p>

                        <h2 class="mt-2 text-lg font-semibold text-slate-950">
                            {{ $class->name }}
                        </h2>
                    </div>

                    <span class="rounded-md bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-600">
                        {{ $class->students_count }}
                        {{ Str::plural('student', $class->students_count) }}
                    </span>
                </div>

                <div class="mt-6 border-t border-slate-100 pt-4">
                    <p class="text-xs uppercase tracking-wider text-slate-400">
                        Class Code
                    </p>

                    <div class="mt-1 flex items-center justify-between">
                        <span class="font-mono text-lg font-semibold tracking-[0.15em] text-slate-800">
                            {{ $class->class_code }}
                        </span>

                        <span class="text-sm font-medium text-indigo-600 transition group-hover:translate-x-1">
                            Open →
                        </span>
                    </div>
                </div>
            </a>

            @endforeach
        </div>

        @endif
    </div>

    {{-- Create Class Modal --}}
    <div
        x-cloak
        x-show="createOpen"
        x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/40 px-4"
        @keydown.escape.window="createOpen = false">
        <div
            @click.outside="createOpen = false"
            class="w-full max-w-lg rounded-xl bg-white p-6 shadow-2xl">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-slate-950">
                        Create Class
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        AI-Q will automatically generate a class code.
                    </p>
                </div>

                <button
                    type="button"
                    @click="createOpen = false"
                    class="text-xl text-slate-400 hover:text-slate-700">
                    ×
                </button>
            </div>

            <form
                method="POST"
                action="{{ route('teacher.classes.store') }}"
                class="mt-6 space-y-5">
                @csrf

                <div>
                    <label
                        for="name"
                        class="text-sm font-medium text-slate-700">
                        Class Name
                    </label>

                    <input
                        id="name"
                        name="name"
                        type="text"
                        value="{{ old('name') }}"
                        placeholder="e.g. BSIT 3A"
                        required
                        class="mt-2 w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label
                        for="subject_id"
                        class="text-sm font-medium text-slate-700">
                        Subject
                    </label>

                    <select
                        id="subject_id"
                        name="subject_id"
                        required
                        class="mt-2 w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">
                            Select a subject
                        </option>

                        @foreach($subjects as $subject)
                        <option
                            value="{{ $subject->id }}"
                            @selected(old('subject_id')==$subject->id)
                            >
                            {{ $subject->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end gap-3 border-t border-slate-100 pt-5">
                    <button
                        type="button"
                        @click="createOpen = false"
                        class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        Cancel
                    </button>

                    <button
                        type="submit"
                        class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                        Create Class
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection