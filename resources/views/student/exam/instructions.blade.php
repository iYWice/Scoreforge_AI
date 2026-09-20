@extends('layouts.app')

@section('title', 'Exam Instructions')
@section('eyebrow', 'Examination')

@section('content')

<div
    x-data="{ agreed: false, submitting: false }"
    class="mx-auto max-w-5xl">

    {{-- ====================================================== --}}
    {{-- EXAM IDENTITY                                          --}}
    {{-- ====================================================== --}}

    <section class="border-b border-slate-200 pb-7">

        <div class="flex flex-col gap-5
                    lg:flex-row lg:items-end lg:justify-between">

            <div>

                <p class="text-[10px] font-extrabold uppercase
                          tracking-[0.22em] text-indigo-600">
                    Before You Begin
                </p>

                <h1 class="mt-2 text-3xl font-black
                           tracking-[-0.055em] text-slate-950
                           sm:text-4xl">
                    {{ $exam->title }}
                </h1>

                <p class="mt-3 max-w-2xl text-sm leading-6
                          text-slate-500">
                    Review the examination details and instructions
                    carefully before starting your attempt.
                </p>

            </div>


            <div class="text-left lg:text-right">

                <p class="text-[9px] font-extrabold uppercase
                          tracking-[0.18em] text-slate-400">
                    Exam Code
                </p>

                <p class="mt-1 font-mono text-sm font-black
                          tracking-[0.12em] text-indigo-600">
                    {{ $exam->exam_code }}
                </p>

            </div>

        </div>

    </section>



    {{-- ====================================================== --}}
    {{-- EXAM PARAMETERS                                        --}}
    {{-- ====================================================== --}}

    <section class="py-7">

        <p class="text-[10px] font-extrabold uppercase
                  tracking-[0.2em] text-slate-400">
            Examination Parameters
        </p>


        <div class="mt-5 grid grid-cols-2 gap-x-6 gap-y-6
                    border-y border-slate-200 py-6 sm:grid-cols-4">

            {{-- Questions --}}
            <div class="border-r border-slate-200 pr-4">

                <p class="text-[9px] font-extrabold uppercase
                          tracking-[0.16em] text-slate-400">
                    Questions
                </p>

                <p class="mt-2 text-2xl font-black
                          tracking-[-0.04em] text-slate-950">
                    {{ $exam->questions()->count() }}
                </p>

                <p class="mt-1 text-[10px] text-slate-400">
                    Total items
                </p>

            </div>


            {{-- Duration --}}
            <div class="border-r border-slate-200 pr-4">

                <p class="text-[9px] font-extrabold uppercase
                          tracking-[0.16em] text-slate-400">
                    Duration
                </p>

                <p class="mt-2 text-2xl font-black
                          tracking-[-0.04em] text-slate-950">
                    {{ $exam->duration }}
                    <span class="text-sm text-slate-400">
                        min
                    </span>
                </p>

                <p class="mt-1 text-[10px] text-slate-400">
                    Time limit
                </p>

            </div>


            {{-- Passing score --}}
            <div class="border-r border-slate-200 pr-4">

                <p class="text-[9px] font-extrabold uppercase
                          tracking-[0.16em] text-slate-400">
                    Passing Score
                </p>

                <p class="mt-2 text-2xl font-black
                          tracking-[-0.04em] text-emerald-600">
                    {{ $exam->passing_score }}
                    <span class="text-sm text-emerald-400">
                        pts
                    </span>
                </p>

                <p class="mt-1 text-[10px] text-slate-400">
                    Required score
                </p>

            </div>


            {{-- Code --}}
            <div>

                <p class="text-[9px] font-extrabold uppercase
                          tracking-[0.16em] text-slate-400">
                    Assessment
                </p>

                <p class="mt-2 font-mono text-sm font-black
                          tracking-[0.1em] text-indigo-600">
                    {{ $exam->exam_code }}
                </p>

                <p class="mt-1 text-[10px] text-slate-400">
                    Examination code
                </p>

            </div>

        </div>

    </section>



    {{-- ====================================================== --}}
    {{-- MAIN INSTRUCTIONS                                      --}}
    {{-- ====================================================== --}}

    <section class="grid grid-cols-1 gap-8
                    lg:grid-cols-[minmax(0,1fr)_330px]">

        {{-- Rules --}}
        <div>

            <div class="mb-5">

                <p class="text-[10px] font-extrabold uppercase
                          tracking-[0.2em] text-indigo-600">
                    Instructions
                </p>

                <h2 class="mt-1 text-xl font-extrabold
                           tracking-tight text-slate-950">
                    Examination guidelines
                </h2>

                <p class="mt-2 text-sm text-slate-500">
                    Keep these rules in mind throughout your attempt.
                </p>

            </div>


            <div class="border-t border-slate-200">

                {{-- Rule 1 --}}
                <div class="group flex gap-4 border-b
                            border-slate-200 py-5">

                    <span class="flex h-7 w-7 shrink-0
                                 items-center justify-center
                                 rounded-full bg-slate-100
                                 text-[10px] font-black
                                 text-slate-500
                                 transition-colors
                                 group-hover:bg-indigo-100
                                 group-hover:text-indigo-700">
                        01
                    </span>

                    <div>

                        <p class="text-sm font-bold text-slate-800">
                            Read each question carefully
                        </p>

                        <p class="mt-1 text-xs leading-5 text-slate-500">
                            Review the complete question and available
                            choices before submitting an answer.
                        </p>

                    </div>

                </div>


                {{-- Rule 2 --}}
                <div class="group flex gap-4 border-b
                            border-slate-200 py-5">

                    <span class="flex h-7 w-7 shrink-0
                                 items-center justify-center
                                 rounded-full bg-slate-100
                                 text-[10px] font-black
                                 text-slate-500
                                 transition-colors
                                 group-hover:bg-indigo-100
                                 group-hover:text-indigo-700">
                        02
                    </span>

                    <div>

                        <p class="text-sm font-bold text-slate-800">
                            Answer according to the question type
                        </p>

                        <p class="mt-1 text-xs leading-5 text-slate-500">
                            Multiple-choice and True/False questions
                            require one answer. Identification questions
                            require a written response.
                        </p>

                    </div>

                </div>


                {{-- Rule 3 --}}
                <div class="group flex gap-4 border-b
                            border-slate-200 py-5">

                    <span class="flex h-7 w-7 shrink-0
                                 items-center justify-center
                                 rounded-full bg-slate-100
                                 text-[10px] font-black
                                 text-slate-500
                                 transition-colors
                                 group-hover:bg-indigo-100
                                 group-hover:text-indigo-700">
                        03
                    </span>

                    <div>

                        <p class="text-sm font-bold text-slate-800">
                            Complete the exam within the time limit
                        </p>

                        <p class="mt-1 text-xs leading-5 text-slate-500">
                            You have {{ $exam->duration }} minutes
                            to complete this examination after starting.
                        </p>

                    </div>

                </div>


                {{-- Rule 4 --}}
                <div class="group flex gap-4 border-b
                            border-slate-200 py-5">

                    <span class="flex h-7 w-7 shrink-0
                                 items-center justify-center
                                 rounded-full bg-slate-100
                                 text-[10px] font-black
                                 text-slate-500
                                 transition-colors
                                 group-hover:bg-indigo-100
                                 group-hover:text-indigo-700">
                        04
                    </span>

                    <div>

                        <p class="text-sm font-bold text-slate-800">
                            Submission is final
                        </p>

                        <p class="mt-1 text-xs leading-5 text-slate-500">
                            Once the examination is submitted, your
                            responses cannot be changed.
                        </p>

                    </div>

                </div>


                {{-- Rule 5 --}}
                <div class="group flex gap-4 border-b
                            border-slate-200 py-5">

                    <span class="flex h-7 w-7 shrink-0
                                 items-center justify-center
                                 rounded-full bg-slate-100
                                 text-[10px] font-black
                                 text-slate-500
                                 transition-colors
                                 group-hover:bg-indigo-100
                                 group-hover:text-indigo-700">
                        05
                    </span>

                    <div>

                        <p class="text-sm font-bold text-slate-800">
                            Results are processed automatically
                        </p>

                        <p class="mt-1 text-xs leading-5 text-slate-500">
                            AI-Q automatically checks your responses
                            against the instructor's answer key and
                            updates your performance analytics.
                        </p>

                    </div>

                </div>

            </div>

        </div>



        {{-- ================================================== --}}
        {{-- START PANEL                                        --}}
        {{-- ================================================== --}}

        <aside>

            <div class="sticky top-28 overflow-hidden
                        border border-slate-200 bg-white">

                <div class="bg-slate-950 p-5 text-white">

                    <p class="text-[9px] font-extrabold uppercase
                              tracking-[0.18em] text-indigo-400">
                        Ready Check
                    </p>

                    <h2 class="mt-2 text-lg font-extrabold">
                        Begin examination
                    </h2>

                    <p class="mt-2 text-xs leading-5 text-slate-400">
                        The timer begins when your examination
                        attempt is created.
                    </p>

                </div>


                <form
                    method="POST"
                    action="{{ route('student.exam.begin') }}"
                    @submit="submitting = true"
                    class="p-5">

                    @csrf

                    <input
                        type="hidden"
                        name="exam_id"
                        value="{{ $exam->id }}">


                    {{-- Agreement --}}
                    <label
                        class="group flex cursor-pointer
                               items-start gap-3">

                        <input
                            type="checkbox"
                            name="agreement"
                            value="1"
                            required
                            x-model="agreed"
                            class="mt-0.5 h-4 w-4 rounded
                                   border-slate-300
                                   text-indigo-600
                                   focus:ring-indigo-500">


                        <span class="text-xs font-medium
                                     leading-5 text-slate-600
                                     transition-colors
                                     group-hover:text-slate-900">

                            I have read and understood the examination
                            instructions and agree to follow them.

                        </span>

                    </label>


                    <div class="my-5 border-t border-slate-100"></div>


                    {{-- Status --}}
                    <div class="mb-4 flex items-center gap-2">

                        <span
                            class="h-2 w-2 rounded-full
                                   transition-colors"
                            :class="agreed
                                ? 'bg-emerald-500'
                                : 'bg-slate-300'">
                        </span>

                        <span
                            class="text-[10px] font-bold
                                   transition-colors"
                            :class="agreed
                                ? 'text-emerald-600'
                                : 'text-slate-400'"
                            x-text="agreed
                                ? 'Ready to begin'
                                : 'Agreement required'">
                        </span>

                    </div>


                    {{-- Begin --}}
                    <button
                        type="submit"
                        :disabled="!agreed || submitting"
                        :class="agreed && !submitting
                            ? 'bg-indigo-600 text-white hover:bg-indigo-500 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-indigo-500/20 active:translate-y-0 active:scale-95'
                            : 'cursor-not-allowed bg-slate-100 text-slate-400'"
                        class="flex w-full items-center
                               justify-center gap-2 rounded-xl
                               px-5 py-3 text-xs font-bold
                               transition-all duration-200">


                        {{-- Loading --}}
                        <svg
                            x-cloak
                            x-show="submitting"
                            class="h-4 w-4 animate-spin"
                            viewBox="0 0 24 24"
                            fill="none">

                            <circle
                                class="opacity-25"
                                cx="12"
                                cy="12"
                                r="10"
                                stroke="currentColor"
                                stroke-width="4">
                            </circle>

                            <path
                                class="opacity-75"
                                fill="currentColor"
                                d="M4 12a8 8 0 018-8v4
                                   a4 4 0 00-4 4H4z">
                            </path>

                        </svg>


                        <span
                            x-text="submitting
                                ? 'Starting...'
                                : 'Start Examination'">
                        </span>


                        <span
                            x-show="!submitting && agreed"
                            class="transition-transform
                                   group-hover:translate-x-1">
                            →
                        </span>

                    </button>


                    <p class="mt-3 text-center text-[9px]
                              leading-4 text-slate-400">
                        Starting the examination creates your
                        timed attempt.
                    </p>

                </form>

            </div>

        </aside>

    </section>

</div>

@endsection