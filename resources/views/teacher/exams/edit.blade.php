@extends('layouts.app')

@section('title', 'Edit Exam')

@section('content')

<div class="max-w-3xl mx-auto space-y-6">

    {{-- Breadcrumb & Back Action --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('teacher.exams.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-indigo-600 dark:text-slate-400 dark:hover:text-indigo-400 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back to Exams
        </a>
        <span class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Exam Settings</span>
    </div>

    {{-- Form Card Container --}}
    <div class="bg-white dark:bg-slate-900 shadow-xl shadow-slate-900/5 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 sm:p-8 transition-colors">

        <div class="mb-6 pb-6 border-b border-slate-100 dark:border-slate-800">
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">
                Edit Exam Configurations
            </h1>
            <p class="text-xs font-medium text-slate-400 dark:text-slate-500 mt-1">
                Update assessment metadata, target audience, and duration settings.
            </p>
        </div>

        <form method="POST" action="{{ route('teacher.exams.update', $exam->id) }}" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Exam Title --}}
            <div class="space-y-1.5">
                <label for="title" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                    Exam Title
                </label>
                <input
                    type="text"
                    id="title"
                    name="title"
                    value="{{ old('title', $exam->title) }}"
                    required
                    class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-xs font-medium text-slate-800 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 focus:outline-none transition-all placeholder:text-slate-400"
                    placeholder="e.g. Midterm Comprehensive Assessment">
                @error('title')
                <p class="text-[11px] font-semibold text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Subject & Class Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                {{-- Subject Selection --}}
                <div class="space-y-1.5">
                    <label for="subject_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                        Subject
                    </label>
                    <div class="relative">
                        <select
                            id="subject_id"
                            name="subject_id"
                            required
                            class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-xs font-medium text-slate-800 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 focus:outline-none transition-all appearance-none cursor-pointer">
                            @foreach($subjects as $subject)
                            <option
                                value="{{ $subject->id }}"
                                {{ old('subject_id', $exam->subject_id) == $subject->id ? 'selected' : '' }}>
                                {{ $subject->name }}
                            </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                    @error('subject_id')
                    <p class="text-[11px] font-semibold text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Class Selection --}}
                <div class="space-y-1.5">
                    <label for="class_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                        Class / Section
                    </label>
                    <div class="relative">
                        <select
                            id="class_id"
                            name="class_id"
                            required
                            class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-xs font-medium text-slate-800 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 focus:outline-none transition-all appearance-none cursor-pointer">
                            @foreach($classes as $class)
                            <option
                                value="{{ $class->id }}"
                                {{ old('class_id', $exam->class_id) == $class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                    @error('class_id')
                    <p class="text-[11px] font-semibold text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            {{-- Duration & Passing Score Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                {{-- Duration --}}
                <div class="space-y-1.5">
                    <label for="duration" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                        Duration (Minutes)
                    </label>
                    <input
                        type="number"
                        id="duration"
                        name="duration"
                        value="{{ old('duration', $exam->duration) }}"
                        min="1"
                        required
                        class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-xs font-medium text-slate-800 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 focus:outline-none transition-all">
                    @error('duration')
                    <p class="text-[11px] font-semibold text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Passing Score --}}
                <div class="space-y-1.5">
                    <label for="passing_score" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                        Passing Score
                    </label>
                    <input
                        type="number"
                        id="passing_score"
                        name="passing_score"
                        value="{{ old('passing_score', $exam->passing_score) }}"
                        min="0"
                        required
                        class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-xs font-medium text-slate-800 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 focus:outline-none transition-all">
                    @error('passing_score')
                    <p class="text-[11px] font-semibold text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            {{-- Action Buttons --}}
            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100 dark:border-slate-800">
                <a href="{{ route('teacher.exams.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    Cancel
                </a>
                <button
                    type="submit"
                    class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 text-white font-semibold text-xs px-6 py-2.5 rounded-xl shadow-lg shadow-indigo-500/20 transition-all duration-200 hover:scale-[1.02] active:scale-95 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Save Changes</span>
                </button>
            </div>

        </form>

    </div>

</div>

@endsection