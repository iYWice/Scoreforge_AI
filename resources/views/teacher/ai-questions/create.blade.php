@extends('layouts.app')

@section('title', 'AI Question Builder')
@section('eyebrow', 'Exam Workspace')

@section('content')

<div
    class="mx-auto max-w-4xl"
    x-data="{
        fileName: '',
        generating: false
    }">

    {{-- Back --}}
    <div class="mb-6">
        <a
            href="{{ route('teacher.exams.show', $exam) }}"
            class="text-sm font-semibold text-slate-500
                   transition hover:text-indigo-600">
            ← Back to Exam
        </a>
    </div>


    {{-- Exam --}}
    <div class="mb-8">

        <p class="text-sm font-medium text-indigo-600">
            {{ $exam->subject?->name ?? 'No Subject' }}
        </p>

        <h2
            class="mt-1 text-2xl font-bold
                   tracking-tight text-slate-950">
            {{ $exam->title }}
        </h2>

        <p class="mt-2 text-sm text-slate-500">
            Generate draft examination questions
            from your learning material.
        </p>

    </div>


    {{-- Validation --}}
    @if ($errors->any())

    <div
        class="mb-6 rounded-xl border border-red-200
                   bg-red-50 p-4">
        <p class="font-semibold text-red-800">
            Question generation failed.
        </p>

        <ul
            class="mt-2 list-disc space-y-1
                       pl-5 text-sm text-red-700">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>

    @endif


    <form
        method="POST"
        action="{{ route(
            'teacher.ai-questions.generate',
            $exam
        ) }}"
        enctype="multipart/form-data"
        @submit="generating = true"
        class="space-y-6">

        @csrf


        {{-- PDF --}}
        <section
            class="rounded-2xl border border-slate-200
                   bg-white p-6">

            <div class="mb-5">
                <h3
                    class="font-semibold text-slate-950">
                    Learning Material
                </h3>

                <p
                    class="mt-1 text-sm text-slate-500">
                    Upload a PDF containing the material
                    AI-Q should use to generate questions.
                </p>
            </div>


            <label
                class="flex cursor-pointer flex-col
                       items-center justify-center
                       rounded-xl border-2 border-dashed
                       border-slate-300 px-6 py-10
                       text-center transition
                       hover:border-indigo-400
                       hover:bg-indigo-50/40">

                <input
                    type="file"
                    name="source_file"
                    accept=".pdf,application/pdf"
                    required
                    class="hidden"

                    @change="
                        fileName =
                            $event.target.files[0]
                            ? $event.target.files[0].name
                            : ''
                    ">

                <div
                    class="text-sm font-semibold
                           text-slate-700"
                    x-text="
                        fileName
                            ? fileName
                            : 'Choose PDF'
                    ">
                    Choose PDF
                </div>

                <p
                    class="mt-2 text-xs text-slate-500"
                    x-show="!fileName">
                    Maximum file size: 10 MB
                </p>

                <p
                    class="mt-2 text-xs font-medium
                           text-indigo-600"
                    x-show="fileName"
                    x-cloak>
                    PDF selected
                </p>

            </label>

        </section>


        {{-- Configuration --}}
        <section
            class="rounded-2xl border border-slate-200
                   bg-white p-6">

            <h3
                class="mb-5 font-semibold
                       text-slate-950">
                Generation Settings
            </h3>


            {{-- Count --}}
            <div class="mb-6">

                <label
                    class="mb-2 block text-sm
                           font-semibold text-slate-700">
                    Number of Questions
                </label>

                <input
                    type="number"
                    name="question_count"
                    min="1"
                    max="50"
                    value="{{ old(
                        'question_count',
                        10
                    ) }}"
                    required
                    class="w-full rounded-xl
                           border-slate-300
                           focus:border-indigo-500
                           focus:ring-indigo-500">

            </div>


            {{-- Types --}}
            <div class="mb-6">

                <label
                    class="mb-3 block text-sm
                           font-semibold text-slate-700">
                    Question Types
                </label>


                <div
                    class="grid gap-3 sm:grid-cols-3">

                    <label
                        class="flex cursor-pointer
                               items-center gap-3
                               rounded-xl border
                               border-slate-200 p-4">

                        <input
                            type="checkbox"
                            name="question_types[]"
                            value="multiple_choice"
                            checked
                            class="rounded
                                   border-slate-300
                                   text-indigo-600
                                   focus:ring-indigo-500">

                        <span
                            class="text-sm font-medium
                                   text-slate-700">
                            Multiple Choice
                        </span>

                    </label>


                    <label
                        class="flex cursor-pointer
                               items-center gap-3
                               rounded-xl border
                               border-slate-200 p-4">

                        <input
                            type="checkbox"
                            name="question_types[]"
                            value="true_false"
                            checked
                            class="rounded
                                   border-slate-300
                                   text-indigo-600
                                   focus:ring-indigo-500">

                        <span
                            class="text-sm font-medium
                                   text-slate-700">
                            True / False
                        </span>

                    </label>


                    <label
                        class="flex cursor-pointer
                               items-center gap-3
                               rounded-xl border
                               border-slate-200 p-4">

                        <input
                            type="checkbox"
                            name="question_types[]"
                            value="identification"
                            checked
                            class="rounded
                                   border-slate-300
                                   text-indigo-600
                                   focus:ring-indigo-500">

                        <span
                            class="text-sm font-medium
                                   text-slate-700">
                            Identification
                        </span>

                    </label>

                </div>

            </div>


            {{-- Difficulty --}}
            <div>

                <label
                    class="mb-2 block text-sm
                           font-semibold text-slate-700">
                    Difficulty
                </label>

                <select
                    name="difficulty"
                    class="w-full rounded-xl
                           border-slate-300
                           focus:border-indigo-500
                           focus:ring-indigo-500">

                    <option value="">
                        Mixed Difficulty
                    </option>

                    <option value="easy">
                        Easy
                    </option>

                    <option value="medium">
                        Medium
                    </option>

                    <option value="hard">
                        Hard
                    </option>

                </select>

            </div>

        </section>


        {{-- Notice --}}
        <div
            class="rounded-xl border border-indigo-100
                   bg-indigo-50 px-5 py-4">
            <p
                class="text-sm leading-6
                       text-indigo-900">
                Generated questions are drafts.
                They will not be added to the exam
                until you review and approve them.
            </p>
        </div>


        {{-- Generate --}}
        <div class="flex justify-end">

            <button
                type="submit"
                :disabled="generating"
                class="rounded-xl bg-indigo-600
                       px-6 py-3 text-sm font-semibold
                       text-white transition
                       hover:bg-indigo-700
                       disabled:cursor-not-allowed
                       disabled:opacity-60">

                <span x-show="!generating">
                    ✦ Generate Questions
                </span>

                <span
                    x-show="generating"
                    x-cloak>
                    Generating Questions...
                </span>

            </button>

        </div>

    </form>

</div>

@endsection