@extends('layouts.app')

@section('title', $exam->title)
@section('eyebrow', 'Exam Workspace')

@section('page-actions')

<div class="flex items-center gap-2">

    <a
        href="{{ route('teacher.exams.index') }}"
        class="inline-flex items-center gap-2
               rounded-xl border border-slate-200
               bg-white px-3.5 py-2.5
               text-xs font-bold text-slate-600
               transition-all duration-200
               hover:-translate-y-0.5
               hover:border-slate-300
               hover:text-slate-950
               hover:shadow-sm
               active:translate-y-0
               active:scale-95">

        <span class="transition-transform
                     group-hover:-translate-x-1">
            ←
        </span>

        Exams

    </a>

    <a
        href="{{ route('teacher.exams.questions', $exam->id) }}"
        class="group inline-flex items-center gap-2
               rounded-xl bg-slate-950
               px-4 py-2.5
               text-xs font-bold text-white
               transition-all duration-200
               hover:-translate-y-0.5
               hover:bg-indigo-600
               hover:shadow-lg
               hover:shadow-indigo-200
               active:translate-y-0
               active:scale-95">

        Manage Questions

        <span class="transition-transform duration-200
                     group-hover:translate-x-1">
            →
        </span>

    </a>

</div>

@endsection


@section('content')

@php

$attempts = $exam->attempts;

$totalAttempts = $attempts->count();

$completedAttempts = $attempts
->where('status', 'completed');

$completedCount = $completedAttempts->count();

$ongoingCount = $attempts
->where('status', 'ongoing')
->count();


/*
* Calculate an average percentage safely.
*/
$attemptPercentages = $completedAttempts
->map(function ($attempt) {

if (($attempt->total_score ?? 0) <= 0) {
    return null;
    }

    return
    (($attempt->score ?? 0) /
    $attempt->total_score) * 100;

    })
    ->filter(function ($percentage) {
    return $percentage !== null;
    });


    $workspaceAverage =
    $attemptPercentages->count() > 0
    ? $attemptPercentages->average()
    : 0;


    $status = strtolower($exam->status ?? 'draft');

    @endphp


    <div
        x-data="{
            tab: '{{ request('tab') === 'responses' ? 'responses' : 'overview' }}',
        copied: false,

        copyCode() {

            const code = '{{ $exam->exam_code }}';

            navigator.clipboard.writeText(code);

            this.copied = true;

            setTimeout(() => {
                this.copied = false;
            }, 1800);
        }
    }"
        class="space-y-8">


        {{-- ====================================================== --}}
        {{-- EXAM IDENTITY                                          --}}
        {{-- ====================================================== --}}

        <section
            class="relative overflow-hidden
               border-b border-slate-200 pb-8">

            <div
                class="flex flex-col gap-6
                   lg:flex-row lg:items-end
                   lg:justify-between">


                {{-- Exam Information --}}
                <div class="max-w-3xl">

                    <div class="flex flex-wrap items-center gap-3">


                        {{-- Status --}}
                        @if($status === 'published')

                        <span
                            class="inline-flex items-center gap-2
                                   text-[10px] font-extrabold
                                   uppercase tracking-[0.18em]
                                   text-emerald-700">

                            <span class="relative flex h-2 w-2">

                                <span
                                    class="absolute inline-flex
                                           h-full w-full
                                           animate-ping rounded-full
                                           bg-emerald-400
                                           opacity-40">
                                </span>

                                <span
                                    class="relative inline-flex
                                           h-2 w-2 rounded-full
                                           bg-emerald-500">
                                </span>

                            </span>

                            Published

                        </span>

                        @elseif($status === 'draft')

                        <span
                            class="inline-flex items-center gap-2
                                   text-[10px] font-extrabold
                                   uppercase tracking-[0.18em]
                                   text-amber-700">

                            <span
                                class="h-2 w-2 rounded-full
                                       bg-amber-500">
                            </span>

                            Draft

                        </span>

                        @else

                        <span
                            class="inline-flex items-center gap-2
                                   text-[10px] font-extrabold
                                   uppercase tracking-[0.18em]
                                   text-slate-500">

                            <span
                                class="h-2 w-2 rounded-full
                                       bg-slate-400">
                            </span>

                            {{ ucfirst($status) }}

                        </span>

                        @endif


                        <span class="text-slate-300">/</span>


                        @if($exam->subject)

                        <span
                            class="text-xs font-semibold
                                   text-slate-500">
                            {{ $exam->subject->name }}
                        </span>

                        @endif


                        @if($exam->class)

                        <span class="text-slate-300">/</span>

                        <span
                            class="text-xs font-semibold
                                   text-slate-500">
                            {{ $exam->class->name }}
                        </span>

                        @endif

                    </div>


                    <h2
                        class="mt-4 text-3xl font-black
                           tracking-[-0.055em]
                           text-slate-950
                           sm:text-4xl">
                        {{ $exam->title }}
                    </h2>


                    <p
                        class="mt-3 max-w-xl
                           text-sm leading-6
                           text-slate-500">
                        Manage this examination, monitor student
                        responses and review its performance.
                    </p>

                </div>



                {{-- Exam Code --}}
                <button
                    type="button"
                    @click="copyCode()"
                    class="group flex items-center gap-4
                       border border-slate-200
                       bg-white px-5 py-4
                       text-left
                       transition-all duration-300
                       hover:-translate-y-1
                       hover:border-indigo-300
                       hover:shadow-lg
                       hover:shadow-slate-200/60
                       active:translate-y-0
                       active:scale-[0.98]">

                    <div>

                        <p
                            class="text-[9px] font-extrabold
                               uppercase tracking-[0.2em]
                               text-slate-400">
                            Exam Access Code
                        </p>

                        <p
                            class="mt-1 font-mono text-lg
                               font-black tracking-[0.1em]
                               text-slate-950
                               transition-colors
                               group-hover:text-indigo-600">
                            {{ $exam->exam_code }}
                        </p>

                    </div>


                    <div
                        class="flex h-9 w-9 items-center
                           justify-center rounded-full
                           bg-slate-100 text-slate-500
                           transition-all duration-300
                           group-hover:rotate-6
                           group-hover:bg-indigo-600
                           group-hover:text-white">

                        <svg
                            x-show="!copied"
                            class="h-4 w-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M8 7V5a2 2 0 012-2h7a2 2 0 012 2v7a2 2 0 01-2 2h-2M5 8h7a2 2 0 012 2v7a2 2 0 01-2 2H5a2 2 0 01-2-2v-7a2 2 0 012-2z" />

                        </svg>

                        <span
                            x-cloak
                            x-show="copied"
                            class="font-black">
                            ✓
                        </span>

                    </div>


                    <span
                        x-cloak
                        x-show="copied"
                        x-transition
                        class="absolute -mt-16
                           rounded-lg bg-slate-950
                           px-2.5 py-1.5
                           text-[10px] font-bold
                           text-white shadow-lg">
                        Copied!
                    </span>

                </button>

            </div>

        </section>



        {{-- ====================================================== --}}
        {{-- EXAM NAVIGATION                                        --}}
        {{-- ====================================================== --}}

        <nav
            class="flex items-center gap-1
               overflow-x-auto border-b
               border-slate-200">


            {{-- Overview --}}
            <button
                type="button"
                @click="tab = 'overview'"
                :class="tab === 'overview'
                ? 'text-indigo-700'
                : 'text-slate-500 hover:text-slate-950'"
                class="relative shrink-0
                   px-4 py-4
                   text-xs font-bold
                   transition-all
                   active:scale-95">

                Overview

                <span
                    x-show="tab === 'overview'"
                    class="absolute inset-x-3
                       bottom-0 h-[2px]
                       bg-indigo-600">
                </span>

            </button>



            {{-- Questions --}}
            <a
                href="{{ route('teacher.exams.questions', $exam->id) }}"
                class="relative shrink-0
                   px-4 py-4
                   text-xs font-bold
                   text-slate-500
                   transition-all
                   hover:text-indigo-600
                   active:scale-95">

                Questions

                <span
                    class="ml-1 rounded-full
                       bg-slate-100
                       px-2 py-0.5
                       text-[9px]">
                    {{ $exam->questions_count ?? $exam->questions->count() }}
                </span>

            </a>



            {{-- Responses --}}
            <button
                type="button"
                @click="tab = 'responses'"
                :class="tab === 'responses'
                ? 'text-indigo-700'
                : 'text-slate-500 hover:text-slate-950'"
                class="relative shrink-0
                   px-4 py-4
                   text-xs font-bold
                   transition-all
                   active:scale-95">

                Responses

                <span
                    class="ml-1 rounded-full
                       bg-slate-100
                       px-2 py-0.5
                       text-[9px]">
                    {{ $totalAttempts }}
                </span>

                <span
                    x-show="tab === 'responses'"
                    class="absolute inset-x-3
                       bottom-0 h-[2px]
                       bg-indigo-600">
                </span>

            </button>



            {{-- Analysis --}}
            <a
                href="{{ route('teacher.analytics.exam', $exam->id) }}"
                class="group relative shrink-0
                   px-4 py-4
                   text-xs font-bold
                   text-slate-500
                   transition-all
                   hover:text-indigo-600
                   active:scale-95">

                Analysis

                <span
                    class="ml-1 inline-block
                       transition-transform duration-200
                       group-hover:translate-x-1">
                    ↗
                </span>

            </a>

        </nav>



        {{-- ====================================================== --}}
        {{-- OVERVIEW                                               --}}
        {{-- ====================================================== --}}

        <div
            x-show="tab === 'overview'"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0">


            {{-- Metrics --}}
            <section
                class="grid grid-cols-2 gap-x-6
                   gap-y-7 border-b
                   border-slate-200 pb-8
                   lg:grid-cols-4">


                {{-- Questions --}}
                <a
                    href="{{ route('teacher.exams.questions', $exam->id) }}"
                    class="group border-r
                       border-slate-200 pr-5
                       transition-all
                       active:scale-[0.97]">

                    <p
                        class="text-[9px] font-extrabold
                           uppercase tracking-[0.18em]
                           text-slate-400">
                        Questions
                    </p>

                    <div class="mt-2 flex items-center
                            justify-between">

                        <p
                            class="text-3xl font-black
                               tracking-[-0.05em]
                               text-slate-950
                               transition-colors
                               group-hover:text-indigo-600">
                            {{ $exam->questions_count ?? $exam->questions->count() }}
                        </p>

                        <span
                            class="text-slate-300
                               transition-all
                               group-hover:translate-x-1
                               group-hover:text-indigo-600">
                            →
                        </span>

                    </div>

                    <p class="mt-1 text-xs text-slate-400">
                        Assessment items
                    </p>

                </a>



                {{-- Responses --}}
                <button
                    type="button"
                    @click="tab = 'responses'"
                    class="group border-r
                       border-slate-200 pr-5
                       text-left
                       transition-all
                       active:scale-[0.97]">

                    <p
                        class="text-[9px] font-extrabold
                           uppercase tracking-[0.18em]
                           text-slate-400">
                        Responses
                    </p>

                    <div class="mt-2 flex items-center
                            justify-between">

                        <p
                            class="text-3xl font-black
                               tracking-[-0.05em]
                               text-slate-950
                               transition-colors
                               group-hover:text-indigo-600">
                            {{ $totalAttempts }}
                        </p>

                        <span
                            class="text-slate-300
                               transition-all
                               group-hover:translate-x-1
                               group-hover:text-indigo-600">
                            →
                        </span>

                    </div>

                    <p class="mt-1 text-xs text-slate-400">
                        Student attempts
                    </p>

                </button>



                {{-- Average --}}
                <a
                    href="{{ route('teacher.analytics.exam', $exam->id) }}"
                    class="group border-r
                       border-slate-200 pr-5
                       transition-all
                       active:scale-[0.97]">

                    <p
                        class="text-[9px] font-extrabold
                           uppercase tracking-[0.18em]
                           text-slate-400">
                        Average
                    </p>

                    <div class="mt-2 flex items-center
                            justify-between">

                        <p
                            class="text-3xl font-black
                               tracking-[-0.05em]
                               text-slate-950
                               transition-colors
                               group-hover:text-indigo-600">

                            {{ number_format($workspaceAverage, 1) }}%

                        </p>

                        <span
                            class="text-slate-300
                               transition-all
                               group-hover:translate-x-1
                               group-hover:text-indigo-600">
                            ↗
                        </span>

                    </div>

                    <p class="mt-1 text-xs text-slate-400">
                        Completed responses
                    </p>

                </a>



                {{-- Duration --}}
                <div>

                    <p
                        class="text-[9px] font-extrabold
                           uppercase tracking-[0.18em]
                           text-slate-400">
                        Duration
                    </p>

                    <p
                        class="mt-2 text-3xl font-black
                           tracking-[-0.05em]
                           text-slate-950">
                        {{ $exam->duration }}
                        <span
                            class="text-sm font-bold
                               tracking-normal
                               text-slate-400">
                            min
                        </span>
                    </p>

                    <p class="mt-1 text-xs text-slate-400">
                        Time limit
                    </p>

                </div>

            </section>



            {{-- Workspace --}}
            <section
                class="mt-8 grid grid-cols-1
                   gap-6 lg:grid-cols-12">


                {{-- Performance --}}
                <a
                    href="{{ route('teacher.analytics.exam', $exam->id) }}"
                    class="group border
                       border-slate-200 bg-white
                       p-6 transition-all duration-300
                       hover:-translate-y-1
                       hover:border-indigo-200
                       hover:shadow-xl
                       hover:shadow-slate-200/60
                       active:translate-y-0
                       active:scale-[0.99]
                       lg:col-span-7">


                    <div class="flex items-start
                            justify-between gap-4">

                        <div>

                            <p
                                class="text-[10px] font-extrabold
                                   uppercase tracking-[0.2em]
                                   text-indigo-600">
                                Performance
                            </p>

                            <h3
                                class="mt-2 text-lg font-extrabold
                                   tracking-tight text-slate-950">
                                Examination performance
                            </h3>

                            <p
                                class="mt-1 text-sm text-slate-500">
                                Based on completed student responses.
                            </p>

                        </div>


                        <span
                            class="flex h-9 w-9 items-center
                               justify-center rounded-full
                               border border-slate-200
                               text-slate-400
                               transition-all duration-300
                               group-hover:rotate-6
                               group-hover:border-indigo-600
                               group-hover:bg-indigo-600
                               group-hover:text-white">
                            ↗
                        </span>

                    </div>



                    <div class="mt-10">

                        <div class="flex items-end
                                justify-between">

                            <div>

                                <p
                                    class="text-5xl font-black
                                       tracking-[-0.07em]
                                       text-slate-950">
                                    {{ number_format($workspaceAverage, 1) }}
                                    <span
                                        class="text-xl text-slate-300">
                                        %
                                    </span>
                                </p>

                                <p
                                    class="mt-2 text-xs font-semibold
                                       text-slate-400">
                                    Average score
                                </p>

                            </div>


                            <p
                                class="hidden text-xs font-bold
                                   text-indigo-600
                                   transition-transform
                                   group-hover:translate-x-1
                                   sm:block">
                                Full analysis →
                            </p>

                        </div>


                        <div
                            class="mt-7 h-2
                               overflow-hidden bg-slate-100">

                            <div
                                class="h-full bg-indigo-600
                                   transition-all duration-1000
                                   group-hover:bg-indigo-500"
                                style="width:
                                {{ max(0, min(100, $workspaceAverage)) }}%">
                            </div>

                        </div>


                        <div
                            class="mt-3 flex justify-between
                               text-[9px] font-bold
                               text-slate-300">
                            <span>0</span>
                            <span>25</span>
                            <span>50</span>
                            <span>75</span>
                            <span>100</span>
                        </div>

                    </div>

                </a>



                {{-- Exam Configuration --}}
                <div
                    class="border border-slate-200
                       bg-slate-950 p-6
                       text-white lg:col-span-5">

                    <div class="flex items-center
                            justify-between">

                        <div>

                            <p
                                class="text-[10px] font-extrabold
                                   uppercase tracking-[0.2em]
                                   text-indigo-400">
                                Configuration
                            </p>

                            <h3
                                class="mt-2 text-lg font-extrabold">
                                Examination setup
                            </h3>

                        </div>


                        <a
                            href="{{ route('teacher.exams.edit', $exam->id) }}"
                            class="rounded-lg border
                               border-slate-700
                               px-3 py-2
                               text-[10px] font-bold
                               text-slate-300
                               transition-all
                               hover:-translate-y-0.5
                               hover:border-indigo-500
                               hover:text-white
                               active:scale-95">
                            Edit
                        </a>

                    </div>



                    <dl class="mt-7 divide-y
                           divide-slate-800">


                        <div class="flex items-center
                                justify-between py-3">

                            <dt
                                class="text-xs font-medium
                                   text-slate-500">
                                Status
                            </dt>

                            <dd
                                class="text-xs font-bold
                                   capitalize text-white">
                                {{ $status }}
                            </dd>

                        </div>


                        <div class="flex items-center
                                justify-between py-3">

                            <dt
                                class="text-xs font-medium
                                   text-slate-500">
                                Duration
                            </dt>

                            <dd class="text-xs font-bold">
                                {{ $exam->duration }} minutes
                            </dd>

                        </div>


                        <div class="flex items-center
                                justify-between py-3">

                            <dt
                                class="text-xs font-medium
                                   text-slate-500">
                                Passing score
                            </dt>

                            <dd class="text-xs font-bold">
                                {{ $exam->passing_score }}
                            </dd>

                        </div>


                        <div class="flex items-center
                                justify-between py-3">

                            <dt
                                class="text-xs font-medium
                                   text-slate-500">
                                Completed
                            </dt>

                            <dd class="text-xs font-bold">
                                {{ $completedCount }}
                            </dd>

                        </div>


                        <div class="flex items-center
                                justify-between py-3">

                            <dt
                                class="text-xs font-medium
                                   text-slate-500">
                                Currently taking
                            </dt>

                            <dd class="flex items-center
                                  gap-2 text-xs font-bold">

                                @if($ongoingCount > 0)

                                <span
                                    class="h-1.5 w-1.5
                                           animate-pulse
                                           rounded-full
                                           bg-amber-400">
                                </span>

                                @endif

                                {{ $ongoingCount }}

                            </dd>

                        </div>

                    </dl>

                </div>

            </section>



            {{-- Recent Responses --}}
            <section class="mt-10">

                <div class="mb-4 flex items-end
                        justify-between">

                    <div>

                        <p
                            class="text-[10px] font-extrabold
                               uppercase tracking-[0.2em]
                               text-slate-400">
                            Recent Activity
                        </p>

                        <h3
                            class="mt-1 text-lg font-extrabold
                               text-slate-950">
                            Student responses
                        </h3>

                    </div>


                    @if($totalAttempts > 0)

                    <button
                        type="button"
                        @click="tab = 'responses'"
                        class="group text-xs font-bold
                               text-indigo-600
                               transition-all
                               hover:text-indigo-700
                               active:scale-95">

                        View all

                        <span
                            class="inline-block
                                   transition-transform
                                   group-hover:translate-x-1">
                            →
                        </span>

                    </button>

                    @endif

                </div>


                @if($totalAttempts > 0)

                <div class="border-t border-slate-200">

                    @foreach($attempts->take(5) as $attempt)

                    <div
                        class="group flex flex-col
                                   gap-3 border-b
                                   border-slate-200
                                   px-2 py-4
                                   transition-all duration-200
                                   hover:bg-white
                                   sm:flex-row
                                   sm:items-center
                                   sm:justify-between">


                        <div class="flex items-center gap-3">

                            <div
                                class="flex h-9 w-9
                                           shrink-0 items-center
                                           justify-center
                                           rounded-full
                                           bg-slate-100
                                           text-[10px]
                                           font-extrabold
                                           text-slate-600
                                           transition-all
                                           group-hover:bg-indigo-50
                                           group-hover:text-indigo-600">

                                @if($attempt->student)

                                {{ strtoupper(substr($attempt->student->fname, 0, 1)) }}
                                {{ strtoupper(substr($attempt->student->lname, 0, 1)) }}

                                @else

                                ?

                                @endif

                            </div>


                            <div>

                                <p
                                    class="text-sm font-bold
                                               text-slate-800">

                                    @if($attempt->student)

                                    {{ $attempt->student->fname }}
                                    {{ $attempt->student->lname }}

                                    @else

                                    Student unavailable

                                    @endif

                                </p>


                                <p
                                    class="mt-0.5 text-[10px]
                                               font-medium
                                               text-slate-400">

                                    @if($attempt->started_at)

                                    {{ \Carbon\Carbon::parse($attempt->started_at)->format('M d, Y • h:i A') }}

                                    @else

                                    Start time unavailable

                                    @endif

                                </p>

                            </div>

                        </div>



                        <div class="flex items-center gap-4">

                            @if($attempt->status === 'ongoing')

                            <span
                                class="inline-flex
                                               items-center gap-1.5
                                               text-[10px] font-bold
                                               text-amber-600">

                                <span
                                    class="h-1.5 w-1.5
                                                   animate-pulse
                                                   rounded-full
                                                   bg-amber-500">
                                </span>

                                Taking exam

                            </span>

                            @elseif($attempt->status === 'completed')

                            <span
                                class="inline-flex
                                               items-center gap-1.5
                                               text-[10px] font-bold
                                               text-emerald-600">

                                <span
                                    class="h-1.5 w-1.5
                                                   rounded-full
                                                   bg-emerald-500">
                                </span>

                                Completed

                            </span>

                            @else

                            <span
                                class="text-[10px]
                                               font-bold capitalize
                                               text-slate-500">
                                {{ $attempt->status }}
                            </span>

                            @endif


                            <div
                                class="min-w-[70px]
                                           text-right">

                                @if($attempt->status === 'completed')

                                <span
                                    class="text-sm font-black
                                                   text-slate-950">
                                    {{ $attempt->score ?? 0 }}
                                </span>

                                <span
                                    class="text-xs
                                                   text-slate-400">
                                    /{{ $attempt->total_score ?? 0 }}
                                </span>

                                @else

                                <span class="text-slate-300">
                                    —
                                </span>

                                @endif

                            </div>

                        </div>

                    </div>

                    @endforeach

                </div>

                @else

                <div
                    class="border-y border-slate-200
                           py-12 text-center">

                    <p
                        class="text-sm font-bold
                               text-slate-700">
                        No responses yet.
                    </p>

                    <p
                        class="mt-1 text-xs
                               text-slate-400">
                        Share
                        <strong class="font-mono
                                      text-indigo-600">
                            {{ $exam->exam_code }}
                        </strong>
                        with students when the examination is ready.
                    </p>

                </div>

                @endif

            </section>

        </div>



        {{-- ====================================================== --}}
        {{-- RESPONSES TAB                                          --}}
        {{-- ====================================================== --}}

        <div
            x-cloak
            x-show="tab === 'responses'"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0">


            <section>

                <div
                    class="mb-6 flex flex-col gap-3
                       sm:flex-row sm:items-end
                       sm:justify-between">

                    <div>

                        <p
                            class="text-[10px] font-extrabold
                               uppercase tracking-[0.2em]
                               text-indigo-600">
                            Student Activity
                        </p>

                        <h3
                            class="mt-1 text-xl font-extrabold
                               tracking-tight text-slate-950">
                            Examination responses
                        </h3>

                        <p
                            class="mt-1 text-sm text-slate-500">
                            {{ $totalAttempts }}
                            total student
                            {{ Str::plural('attempt', $totalAttempts) }}.
                        </p>

                    </div>


                    <div class="flex items-center gap-5">

                        <div class="text-right">

                            <p
                                class="text-[9px] font-extrabold
                                   uppercase tracking-wider
                                   text-slate-400">
                                Completed
                            </p>

                            <p
                                class="mt-1 text-lg font-black
                                   text-slate-950">
                                {{ $completedCount }}
                            </p>

                        </div>

                        <div
                            class="h-8 w-px bg-slate-200">
                        </div>

                        <div class="text-right">

                            <p
                                class="text-[9px] font-extrabold
                                   uppercase tracking-wider
                                   text-slate-400">
                                Ongoing
                            </p>

                            <p
                                class="mt-1 text-lg font-black
                                   text-slate-950">
                                {{ $ongoingCount }}
                            </p>

                        </div>

                    </div>

                </div>



                @if($totalAttempts > 0)

                <div
                    class="overflow-hidden border
                           border-slate-200 bg-white">


                    <div class="overflow-x-auto">

                        <table
                            class="w-full border-collapse
                                   text-left">


                            <thead>

                                <tr
                                    class="border-b
                                           border-slate-200
                                           bg-slate-50
                                           text-[9px]
                                           font-extrabold
                                           uppercase
                                           tracking-[0.15em]
                                           text-slate-400">

                                    <th class="px-5 py-3.5">
                                        Student
                                    </th>

                                    <th class="px-5 py-3.5">
                                        Started
                                    </th>

                                    <th class="px-5 py-3.5">
                                        Status
                                    </th>

                                    <th
                                        class="px-5 py-3.5
                                               text-right">
                                        Score
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                @foreach($attempts as $attempt)

                                <tr
                                    class="group border-b
                                               border-slate-100
                                               transition-colors
                                               last:border-0
                                               hover:bg-indigo-50/30">


                                    {{-- Student --}}
                                    <td class="px-5 py-4">

                                        <div
                                            class="flex items-center
                                                       gap-3">

                                            <div
                                                class="flex h-8 w-8
                                                           shrink-0
                                                           items-center
                                                           justify-center
                                                           rounded-full
                                                           bg-slate-100
                                                           text-[9px]
                                                           font-black
                                                           text-slate-600
                                                           transition-colors
                                                           group-hover:bg-indigo-100
                                                           group-hover:text-indigo-700">

                                                @if($attempt->student)

                                                {{ strtoupper(substr($attempt->student->fname, 0, 1)) }}
                                                {{ strtoupper(substr($attempt->student->lname, 0, 1)) }}

                                                @else

                                                ?

                                                @endif

                                            </div>


                                            <span
                                                class="text-xs
                                                           font-bold
                                                           text-slate-800">

                                                @if($attempt->student)

                                                {{ $attempt->student->fname }}
                                                {{ $attempt->student->lname }}

                                                @else

                                                Student unavailable

                                                @endif

                                            </span>

                                        </div>

                                    </td>



                                    {{-- Started --}}
                                    <td
                                        class="px-5 py-4
                                                   text-xs font-medium
                                                   text-slate-500">

                                        @if($attempt->started_at)

                                        {{ \Carbon\Carbon::parse($attempt->started_at)->format('M d, Y • h:i A') }}

                                        @else

                                        —

                                        @endif

                                    </td>



                                    {{-- Status --}}
                                    <td class="px-5 py-4">

                                        @if($attempt->status === 'ongoing')

                                        <span
                                            class="inline-flex
                                                           items-center
                                                           gap-1.5
                                                           text-[10px]
                                                           font-bold
                                                           text-amber-600">

                                            <span
                                                class="h-1.5 w-1.5
                                                               animate-pulse
                                                               rounded-full
                                                               bg-amber-500">
                                            </span>

                                            Taking exam

                                        </span>

                                        @elseif($attempt->status === 'completed')

                                        <span
                                            class="inline-flex
                                                           items-center
                                                           gap-1.5
                                                           text-[10px]
                                                           font-bold
                                                           text-emerald-600">

                                            <span
                                                class="h-1.5 w-1.5
                                                               rounded-full
                                                               bg-emerald-500">
                                            </span>

                                            Completed

                                        </span>

                                        @else

                                        <span
                                            class="text-[10px]
                                                           font-bold
                                                           capitalize
                                                           text-slate-500">
                                            {{ $attempt->status }}
                                        </span>

                                        @endif

                                    </td>



                                    {{-- Score --}}
                                    <td
                                        class="px-5 py-4
                                                   text-right">

                                        @if($attempt->status === 'completed')

                                        <span
                                            class="text-sm
                                                           font-black
                                                           text-slate-950">
                                            {{ $attempt->score ?? 0 }}
                                        </span>

                                        <span
                                            class="text-xs
                                                           text-slate-400">
                                            /
                                            {{ $attempt->total_score ?? 0 }}
                                        </span>

                                        @else

                                        <span
                                            class="text-slate-300">
                                            —
                                        </span>

                                        @endif

                                    </td>

                                </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                </div>

                @else

                <div
                    class="border-y border-slate-200
                           py-16 text-center">

                    <p
                        class="text-sm font-bold
                               text-slate-700">
                        No student responses recorded.
                    </p>

                    <p
                        class="mt-1 text-xs text-slate-400">
                        Students can access this examination
                        using code
                        <strong
                            class="font-mono
                                   text-indigo-600">
                            {{ $exam->exam_code }}
                        </strong>.
                    </p>

                </div>

                @endif

            </section>

        </div>

    </div>

    @endsection