@extends('layouts.app')

@section('title', 'Take Exam')

@section('content')

@php
    $questions = $attempt->exam->questions;
    $totalQuestions = $questions->count();
@endphp


<div
    x-data="examWorkspace({{ $totalQuestions }})"
    x-init="init()"
    class="mx-auto max-w-6xl">

    <form
        id="examForm"
        method="POST"
        action="{{ route('student.exam.submit', $attempt->id) }}"
        @submit="handleSubmit($event)">

        @csrf


        {{-- ================================================== --}}
        {{-- EXAM HEADER                                        --}}
        {{-- ================================================== --}}

        <header
            class="sticky top-16 z-30 border-b border-slate-200
                   bg-white/95 backdrop-blur-xl">

            <div class="py-4">

                <div class="flex items-center justify-between gap-4">

                    {{-- Exam --}}
                    <div class="min-w-0">

                        <p class="text-[9px] font-extrabold uppercase
                                  tracking-[0.2em] text-indigo-600">
                            Examination Session
                        </p>

                        <h1 class="mt-1 truncate text-sm font-black
                                   text-slate-950 sm:text-base">
                            {{ $attempt->exam->title }}
                        </h1>

                    </div>


                    {{-- Timer --}}
                    <div
                        class="flex shrink-0 items-center gap-3
                               border-l border-slate-200 pl-4">

                        <div class="hidden text-right sm:block">

                            <p class="text-[8px] font-extrabold uppercase
                                      tracking-[0.15em] text-slate-400">
                                Time Remaining
                            </p>

                            <p
                                x-show="!timerCritical"
                                class="mt-0.5 text-[9px] font-semibold
                                       text-slate-400">
                                Exam timer
                            </p>

                            <p
                                x-cloak
                                x-show="timerCritical"
                                class="mt-0.5 text-[9px] font-bold
                                       text-rose-600">
                                Time is running low
                            </p>

                        </div>


                        <div
                            id="timer"
                            class="font-mono text-xl font-black
                                   tracking-tight transition-colors
                                   sm:text-2xl"
                            :class="timerCritical
                                ? 'text-rose-600'
                                : 'text-slate-950'">

                            --:--

                        </div>

                    </div>

                </div>



                {{-- Progress --}}
                <div class="mt-4">

                    <div class="mb-2 flex items-center justify-between">

                        <p class="text-[9px] font-extrabold uppercase
                                  tracking-[0.15em] text-slate-400">

                            Question
                            <span
                                class="text-slate-700"
                                x-text="current + 1">
                            </span>
                            of {{ $totalQuestions }}

                        </p>


                        <p class="text-[9px] font-bold text-slate-400">

                            <span
                                class="text-indigo-600"
                                x-text="answeredCount">
                            </span>

                            answered

                        </p>

                    </div>


                    <div class="h-1 overflow-hidden bg-slate-100">

                        <div
                            class="h-full bg-indigo-600
                                   transition-all duration-500"
                            :style="`width: ${progress}%`">
                        </div>

                    </div>

                </div>

            </div>

        </header>



        {{-- ================================================== --}}
        {{-- EXAM BODY                                          --}}
        {{-- ================================================== --}}

        <div class="grid gap-8 py-8
                    lg:grid-cols-[minmax(0,1fr)_240px]">


            {{-- ============================================== --}}
            {{-- QUESTION                                      --}}
            {{-- ============================================== --}}

            <main class="min-w-0">

                @forelse($questions as $question)

                    <section
                        x-cloak
                        x-show="current === {{ $loop->index }}"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-x-3"
                        x-transition:enter-end="opacity-100 translate-x-0"
                        class="min-h-[470px]">


                        {{-- Question metadata --}}
                        <div
                            class="flex items-center justify-between
                                   border-b border-slate-200 pb-5">

                            <div class="flex items-center gap-3">

                                <span
                                    class="flex h-8 w-8 items-center
                                           justify-center rounded-full
                                           bg-indigo-50 text-[10px]
                                           font-black text-indigo-700">

                                    {{ str_pad(
                                        $loop->iteration,
                                        2,
                                        '0',
                                        STR_PAD_LEFT
                                    ) }}

                                </span>


                                <div>

                                    <p
                                        class="text-[9px] font-extrabold
                                               uppercase tracking-[0.16em]
                                               text-slate-400">

                                        @if($question->question_type === 'mcq')
                                            Multiple Choice
                                        @elseif($question->question_type === 'tf')
                                            True / False
                                        @elseif($question->question_type === 'identification')
                                            Identification
                                        @else
                                            Question
                                        @endif

                                    </p>


                                    @if($question->topic)

                                        <p class="mt-0.5 text-[10px]
                                                  font-bold text-indigo-600">
                                            {{ $question->topic }}
                                        </p>

                                    @endif

                                </div>

                            </div>


                            <span class="text-[10px] font-bold
                                         text-slate-400">

                                {{ $question->points }}

                                {{ \Illuminate\Support\Str::plural(
                                    'point',
                                    $question->points
                                ) }}

                            </span>

                        </div>



                        {{-- Question text --}}
                        <div class="py-8 sm:py-10">

                            <p class="max-w-3xl text-lg font-bold
                                      leading-8 text-slate-900
                                      sm:text-xl sm:leading-9">

                                {{ $question->question_text }}

                            </p>

                        </div>



                        {{-- ================================== --}}
                        {{-- MULTIPLE CHOICE                    --}}
                        {{-- ================================== --}}

                        @if($question->question_type === 'mcq')

                            <div class="space-y-3">

                                @foreach($question->options as $option)

                                    <label
                                        class="group block cursor-pointer">

                                        <input
                                            type="radio"
                                            name="answers[{{ $question->id }}]"
                                            value="{{ $option->option_text }}"
                                            @change="markAnswered({{ $loop->parent->index }})"
                                            class="peer sr-only">


                                        <div
                                            class="flex items-center gap-4
                                                   border border-slate-200
                                                   bg-white px-4 py-4
                                                   transition-all duration-200
                                                   group-hover:-translate-y-0.5
                                                   group-hover:border-indigo-300
                                                   group-hover:shadow-sm
                                                   peer-checked:border-indigo-500
                                                   peer-checked:bg-indigo-50
                                                   peer-checked:ring-2
                                                   peer-checked:ring-indigo-100
                                                   active:translate-y-0
                                                   active:scale-[0.995]">


                                            <span
                                                class="flex h-8 w-8 shrink-0
                                                       items-center justify-center
                                                       rounded-full border
                                                       border-slate-200
                                                       text-[10px] font-black
                                                       text-slate-400
                                                       transition-all
                                                       peer-checked:border-indigo-500">

                                                {{ chr(65 + $loop->index) }}

                                            </span>


                                            <span
                                                class="text-sm font-semibold
                                                       leading-6 text-slate-700
                                                       transition-colors
                                                       peer-checked:text-indigo-900">

                                                {{ $option->option_text }}

                                            </span>


                                            <span
                                                class="ml-auto flex h-5 w-5
                                                       shrink-0 items-center
                                                       justify-center rounded-full
                                                       border border-slate-300
                                                       text-[10px] text-transparent
                                                       transition-all
                                                       peer-checked:border-indigo-600
                                                       peer-checked:bg-indigo-600
                                                       peer-checked:text-white">

                                                ✓

                                            </span>

                                        </div>

                                    </label>

                                @endforeach

                            </div>

                        @endif



                        {{-- ================================== --}}
                        {{-- TRUE / FALSE                       --}}
                        {{-- ================================== --}}

                        @if($question->question_type === 'tf')

                            <div class="grid gap-3 sm:grid-cols-2">


                                <label class="group cursor-pointer">

                                    <input
                                        type="radio"
                                        name="answers[{{ $question->id }}]"
                                        value="True"
                                        @change="markAnswered({{ $loop->index }})"
                                        class="peer sr-only">


                                    <div
                                        class="border border-slate-200
                                               bg-white p-6 text-center
                                               transition-all duration-200
                                               group-hover:-translate-y-0.5
                                               group-hover:border-indigo-300
                                               group-hover:shadow-sm
                                               peer-checked:border-indigo-500
                                               peer-checked:bg-indigo-50
                                               peer-checked:ring-2
                                               peer-checked:ring-indigo-100
                                               active:scale-[0.99]">

                                        <span
                                            class="block text-lg font-black
                                                   text-slate-800
                                                   peer-checked:text-indigo-700">
                                            True
                                        </span>

                                        <span
                                            class="mt-1 block text-[10px]
                                                   font-semibold text-slate-400">
                                            Select True
                                        </span>

                                    </div>

                                </label>



                                <label class="group cursor-pointer">

                                    <input
                                        type="radio"
                                        name="answers[{{ $question->id }}]"
                                        value="False"
                                        @change="markAnswered({{ $loop->index }})"
                                        class="peer sr-only">


                                    <div
                                        class="border border-slate-200
                                               bg-white p-6 text-center
                                               transition-all duration-200
                                               group-hover:-translate-y-0.5
                                               group-hover:border-indigo-300
                                               group-hover:shadow-sm
                                               peer-checked:border-indigo-500
                                               peer-checked:bg-indigo-50
                                               peer-checked:ring-2
                                               peer-checked:ring-indigo-100
                                               active:scale-[0.99]">

                                        <span
                                            class="block text-lg font-black
                                                   text-slate-800">
                                            False
                                        </span>

                                        <span
                                            class="mt-1 block text-[10px]
                                                   font-semibold text-slate-400">
                                            Select False
                                        </span>

                                    </div>

                                </label>

                            </div>

                        @endif



                        {{-- ================================== --}}
                        {{-- IDENTIFICATION                     --}}
                        {{-- ================================== --}}

                        @if($question->question_type === 'identification')

                            <div>

                                <label
                                    class="mb-2 block text-[9px]
                                           font-extrabold uppercase
                                           tracking-[0.16em]
                                           text-slate-400">
                                    Your Answer
                                </label>


                                <input
                                    type="text"
                                    name="answers[{{ $question->id }}]"
                                    @input="markAnswered(
                                        {{ $loop->index }},
                                        $event.target.value
                                    )"
                                    autocomplete="off"
                                    placeholder="Type your answer here..."
                                    class="w-full border border-slate-200
                                           bg-white px-4 py-4 text-sm
                                           font-semibold text-slate-800
                                           outline-none transition-all
                                           placeholder:font-medium
                                           placeholder:text-slate-300
                                           focus:border-indigo-500
                                           focus:ring-4
                                           focus:ring-indigo-100/60">


                                <p class="mt-2 text-[10px]
                                          leading-5 text-slate-400">
                                    Enter your response clearly before
                                    moving to the next question.
                                </p>

                            </div>

                        @endif



                        {{-- ================================== --}}
                        {{-- QUESTION NAVIGATION                 --}}
                        {{-- ================================== --}}

                        <div
                            class="mt-10 flex items-center justify-between
                                   border-t border-slate-200 pt-5">


                            <button
                                type="button"
                                @click="previous()"
                                :disabled="current === 0"
                                :class="current === 0
                                    ? 'cursor-not-allowed text-slate-300'
                                    : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950 active:scale-95'"
                                class="inline-flex items-center gap-2
                                       rounded-xl px-4 py-2.5
                                       text-xs font-bold transition-all">

                                ← Previous

                            </button>



                            @if(!$loop->last)

                                <button
                                    type="button"
                                    @click="next()"
                                    class="group inline-flex items-center
                                           gap-2 rounded-xl bg-slate-950
                                           px-5 py-2.5 text-xs font-bold
                                           text-white transition-all
                                           hover:-translate-y-0.5
                                           hover:bg-indigo-600
                                           hover:shadow-lg
                                           active:translate-y-0
                                           active:scale-95">

                                    Next

                                    <span
                                        class="transition-transform
                                               group-hover:translate-x-1">
                                        →
                                    </span>

                                </button>

                            @else

                                <button
                                    type="button"
                                    @click="openSubmitModal()"
                                    class="group inline-flex items-center
                                           gap-2 rounded-xl bg-indigo-600
                                           px-5 py-2.5 text-xs font-bold
                                           text-white transition-all
                                           hover:-translate-y-0.5
                                           hover:bg-indigo-500
                                           hover:shadow-lg
                                           hover:shadow-indigo-500/20
                                           active:translate-y-0
                                           active:scale-95">

                                    Review & Submit

                                    <span
                                        class="transition-transform
                                               group-hover:translate-x-1">
                                        →
                                    </span>

                                </button>

                            @endif

                        </div>

                    </section>

                @empty

                    <div class="py-20 text-center">

                        <p class="text-sm font-bold text-slate-600">
                            No questions are available for this examination.
                        </p>

                    </div>

                @endforelse

            </main>



            {{-- ============================================== --}}
            {{-- QUESTION NAVIGATOR                             --}}
            {{-- ============================================== --}}

            @if($totalQuestions > 0)

                <aside class="hidden lg:block">

                    <div class="sticky top-44">

                        <p class="text-[9px] font-extrabold uppercase
                                  tracking-[0.18em] text-slate-400">
                            Question Navigator
                        </p>


                        <div class="mt-4 grid grid-cols-5 gap-2">

                            @foreach($questions as $question)

                                <button
                                    type="button"
                                    @click="goTo({{ $loop->index }})"
                                    :class="{
                                        'bg-slate-950 text-white border-slate-950':
                                            current === {{ $loop->index }},

                                        'bg-indigo-50 text-indigo-700 border-indigo-200':
                                            current !== {{ $loop->index }}
                                            && answered[{{ $loop->index }}],

                                        'bg-white text-slate-500 border-slate-200 hover:border-indigo-300 hover:text-indigo-600':
                                            current !== {{ $loop->index }}
                                            && !answered[{{ $loop->index }}]
                                    }"
                                    class="flex h-9 w-9 items-center
                                           justify-center border
                                           text-[10px] font-black
                                           transition-all duration-200
                                           hover:-translate-y-0.5
                                           active:translate-y-0
                                           active:scale-90">

                                    {{ $loop->iteration }}

                                </button>

                            @endforeach

                        </div>



                        <div class="mt-6 space-y-2
                                    border-t border-slate-200 pt-4">

                            <div class="flex items-center gap-2">

                                <span class="h-2.5 w-2.5
                                             bg-slate-950">
                                </span>

                                <span class="text-[9px]
                                             font-semibold text-slate-400">
                                    Current
                                </span>

                            </div>


                            <div class="flex items-center gap-2">

                                <span class="h-2.5 w-2.5
                                             border border-indigo-200
                                             bg-indigo-50">
                                </span>

                                <span class="text-[9px]
                                             font-semibold text-slate-400">
                                    Answered
                                </span>

                            </div>


                            <div class="flex items-center gap-2">

                                <span class="h-2.5 w-2.5
                                             border border-slate-200
                                             bg-white">
                                </span>

                                <span class="text-[9px]
                                             font-semibold text-slate-400">
                                    Unanswered
                                </span>

                            </div>

                        </div>



                        <button
                            type="button"
                            @click="openSubmitModal()"
                            class="mt-6 w-full border
                                   border-slate-200 bg-white
                                   px-3 py-2.5 text-[10px]
                                   font-bold text-slate-600
                                   transition-all
                                   hover:border-indigo-300
                                   hover:text-indigo-700
                                   active:scale-95">

                            Review & Submit

                        </button>

                    </div>

                </aside>

            @endif

        </div>



        {{-- ================================================== --}}
        {{-- MOBILE NAVIGATOR                                   --}}
        {{-- ================================================== --}}

        @if($totalQuestions > 0)

            <div
                class="sticky bottom-3 z-20 mb-4
                       border border-slate-200 bg-white/95
                       p-3 shadow-xl backdrop-blur-xl lg:hidden">

                <div class="flex items-center gap-3">

                    <div class="min-w-0 flex-1">

                        <p class="text-[8px] font-extrabold uppercase
                                  tracking-[0.15em] text-slate-400">
                            Progress
                        </p>

                        <p class="mt-0.5 text-xs font-bold text-slate-700">

                            <span x-text="answeredCount"></span>
                            of {{ $totalQuestions }} answered

                        </p>

                    </div>


                    <button
                        type="button"
                        @click="navigatorOpen = true"
                        class="rounded-xl bg-slate-950
                               px-4 py-2.5 text-[10px]
                               font-bold text-white
                               transition-all active:scale-95">

                        Questions

                    </button>


                    <button
                        type="button"
                        @click="openSubmitModal()"
                        class="rounded-xl bg-indigo-600
                               px-4 py-2.5 text-[10px]
                               font-bold text-white
                               transition-all active:scale-95">

                        Submit

                    </button>

                </div>

            </div>

        @endif



        {{-- ================================================== --}}
        {{-- MOBILE QUESTION NAVIGATOR MODAL                    --}}
        {{-- ================================================== --}}

        <div
            x-cloak
            x-show="navigatorOpen"
            x-transition.opacity
            class="fixed inset-0 z-[80] flex items-end
                   justify-center bg-slate-950/40
                   p-3 backdrop-blur-sm lg:hidden"
            @click.self="navigatorOpen = false">


            <div
                x-show="navigatorOpen"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="translate-y-full"
                x-transition:enter-end="translate-y-0"
                class="w-full max-w-lg bg-white p-5">


                <div class="flex items-center justify-between">

                    <div>

                        <p class="text-[9px] font-extrabold uppercase
                                  tracking-[0.18em] text-indigo-600">
                            Question Navigator
                        </p>

                        <p class="mt-1 text-sm font-bold text-slate-900">
                            {{ $totalQuestions }} questions
                        </p>

                    </div>


                    <button
                        type="button"
                        @click="navigatorOpen = false"
                        class="flex h-8 w-8 items-center
                               justify-center rounded-full
                               text-slate-400
                               transition-all
                               hover:bg-slate-100
                               hover:text-slate-700
                               active:scale-75">

                        ×

                    </button>

                </div>



                <div class="mt-5 grid grid-cols-6 gap-2 sm:grid-cols-8">

                    @foreach($questions as $question)

                        <button
                            type="button"
                            @click="goTo({{ $loop->index }}); navigatorOpen = false"
                            :class="{
                                'bg-slate-950 text-white border-slate-950':
                                    current === {{ $loop->index }},

                                'bg-indigo-50 text-indigo-700 border-indigo-200':
                                    current !== {{ $loop->index }}
                                    && answered[{{ $loop->index }}],

                                'bg-white text-slate-500 border-slate-200':
                                    current !== {{ $loop->index }}
                                    && !answered[{{ $loop->index }}]
                            }"
                            class="flex aspect-square items-center
                                   justify-center border
                                   text-[10px] font-black
                                   transition-all active:scale-90">

                            {{ $loop->iteration }}

                        </button>

                    @endforeach

                </div>



                <div class="mt-5 border-t border-slate-100 pt-4">

                    <p class="text-xs font-semibold text-slate-500">

                        <span
                            class="font-black text-indigo-600"
                            x-text="answeredCount">
                        </span>

                        answered ·

                        <span
                            class="font-black text-slate-700"
                            x-text="unansweredCount">
                        </span>

                        unanswered

                    </p>

                </div>

            </div>

        </div>



        {{-- ================================================== --}}
        {{-- SUBMIT CONFIRMATION                                --}}
        {{-- ================================================== --}}

        <div
            x-cloak
            x-show="submitModal"
            x-transition.opacity
            class="fixed inset-0 z-[90]
                   flex items-center justify-center
                   bg-slate-950/50 p-4
                   backdrop-blur-sm"
            @keydown.escape.window="
                if (!submitting) submitModal = false
            "
            @click.self="
                if (!submitting) submitModal = false
            ">


            <div
                x-show="submitModal"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                class="w-full max-w-md bg-white p-6
                       shadow-2xl">


                <div
                    class="flex h-10 w-10 items-center
                           justify-center rounded-full
                           bg-indigo-50 text-sm
                           font-black text-indigo-600">

                    ✓

                </div>


                <h2 class="mt-5 text-xl font-black
                           tracking-tight text-slate-950">
                    Submit examination?
                </h2>


                <p class="mt-2 text-sm leading-6 text-slate-500">

                    You have answered

                    <strong
                        class="text-slate-800"
                        x-text="answeredCount">
                    </strong>

                    of {{ $totalQuestions }} questions.

                </p>



                {{-- Unanswered warning --}}
                <div
                    x-show="unansweredCount > 0"
                    class="mt-5 border border-amber-200
                           bg-amber-50 p-4">

                    <p class="text-xs font-bold text-amber-800">

                        <span x-text="unansweredCount"></span>

                        <span
                            x-text="unansweredCount === 1
                                ? 'question is'
                                : 'questions are'">
                        </span>

                        still unanswered.

                    </p>

                    <p class="mt-1 text-[10px]
                              leading-5 text-amber-700/80">
                        You may return to the exam and review
                        them before submitting.
                    </p>

                </div>



                <p class="mt-5 text-xs leading-5 text-slate-400">
                    Once submitted, your answers cannot be changed.
                </p>



                <div class="mt-6 flex items-center
                            justify-end gap-3">


                    <button
                        type="button"
                        @click="submitModal = false"
                        :disabled="submitting"
                        class="rounded-xl px-4 py-2.5
                               text-xs font-bold text-slate-500
                               transition-all
                               hover:bg-slate-100
                               hover:text-slate-800
                               active:scale-95">

                        Continue Exam

                    </button>



                    <button
                        type="button"
                        @click="confirmSubmit()"
                        :disabled="submitting"
                        class="inline-flex items-center gap-2
                               rounded-xl bg-indigo-600
                               px-5 py-2.5 text-xs
                               font-bold text-white
                               transition-all
                               hover:bg-indigo-500
                               active:scale-95
                               disabled:cursor-not-allowed
                               disabled:opacity-60">


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
                                ? 'Submitting...'
                                : 'Submit Exam'">
                        </span>

                    </button>

                </div>

            </div>

        </div>

    </form>

</div>



{{-- ========================================================== --}}
{{-- EXAM WORKSPACE                                             --}}
{{-- ========================================================== --}}

<script>

    function examWorkspace(totalQuestions) {

        return {

            current: 0,

            answered: Array(totalQuestions).fill(false),

            submitModal: false,

            navigatorOpen: false,

            submitting: false,

            timerCritical: false,

            seconds: {{ (int) $remainingSeconds }},

            timerInterval: null,


            init() {

                /*
                 * Detect answers that may already exist in the DOM.
                 */
                this.refreshAnswers();

                this.updateTimer();

                this.timerInterval = setInterval(() => {
                    this.updateTimer();
                }, 1000);

            },


            get answeredCount() {

                return this.answered.filter(Boolean).length;

            },


            get unansweredCount() {

                return totalQuestions - this.answeredCount;

            },


            get progress() {

                if (totalQuestions === 0) {
                    return 0;
                }

                return ((this.current + 1) / totalQuestions) * 100;

            },


            goTo(index) {

                if (
                    index < 0 ||
                    index >= totalQuestions
                ) {
                    return;
                }

                this.current = index;

                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });

            },


            next() {

                if (this.current < totalQuestions - 1) {
                    this.goTo(this.current + 1);
                }

            },


            previous() {

                if (this.current > 0) {
                    this.goTo(this.current - 1);
                }

            },


            markAnswered(index, value = null) {

                if (value !== null) {

                    this.answered[index] =
                        String(value).trim().length > 0;

                } else {

                    this.answered[index] = true;

                }

                /*
                 * Force Alpine to observe the array update.
                 */
                this.answered = [...this.answered];

            },


            refreshAnswers() {

                const form =
                    document.getElementById('examForm');

                if (!form) {
                    return;
                }


                @foreach($questions as $question)

                    @if($question->question_type === 'identification')

                        const text{{ $loop->index }} =
                            form.querySelector(
                                '[name="answers[{{ $question->id }}]"]'
                            );

                        if (
                            text{{ $loop->index }} &&
                            text{{ $loop->index }}.value.trim() !== ''
                        ) {
                            this.answered[{{ $loop->index }}] = true;
                        }

                    @else

                        const selected{{ $loop->index }} =
                            form.querySelector(
                                '[name="answers[{{ $question->id }}]"]:checked'
                            );

                        if (selected{{ $loop->index }}) {
                            this.answered[{{ $loop->index }}] = true;
                        }

                    @endif

                @endforeach


                this.answered = [...this.answered];

            },


            openSubmitModal() {

                this.refreshAnswers();

                this.submitModal = true;

            },


            handleSubmit(event) {

                /*
                 * Normal submit attempts should go through
                 * the confirmation modal.
                 *
                 * Timer auto-submit and confirmSubmit use
                 * native form.submit(), which bypasses this.
                 */
                if (!this.submitting) {

                    event.preventDefault();

                    this.openSubmitModal();

                }

            },


            confirmSubmit() {

                if (this.submitting) {
                    return;
                }

                this.submitting = true;

                clearInterval(this.timerInterval);

                document
                    .getElementById('examForm')
                    .submit();

            },


            updateTimer() {

                const timer =
                    document.getElementById('timer');

                if (!timer) {
                    return;
                }


                if (this.seconds <= 0) {

                    timer.textContent = '00:00';

                    this.timerCritical = true;

                    clearInterval(this.timerInterval);

                    this.submitting = true;

                    document
                        .getElementById('examForm')
                        .submit();

                    return;

                }


                /*
                 * Change timer state during final five minutes.
                 */
                this.timerCritical =
                    this.seconds <= 300;


                const minutes =
                    Math.floor(this.seconds / 60);

                const remaining =
                    this.seconds % 60;


                timer.textContent =
                    String(minutes).padStart(2, '0')
                    + ':'
                    + String(remaining).padStart(2, '0');


                this.seconds--;

            }

        };

    }

</script>

@endsection