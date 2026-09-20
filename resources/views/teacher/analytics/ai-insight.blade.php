@extends('layouts.app')

@section('title', 'AI-Q Insight')

@section('eyebrow', 'AI-Assisted Examination Analysis')

@section('page-header')
AI-Q Insight
@endsection


@section('page-actions')

<a
    href="{{ route('teacher.analytics.exam', $exam) }}"
    class="inline-flex items-center gap-2 rounded-xl
               border border-slate-200 bg-white
               px-4 py-2.5 text-xs font-bold
               text-slate-600 transition-all
               hover:-translate-y-0.5
               hover:border-indigo-300
               hover:text-indigo-600
               active:scale-95">

    ← Back to Analysis

</a>

<form
    method="POST"
    action="{{ route(
        'teacher.analytics.ai-insight.regenerate',
        $exam
    ) }}"
    x-data="{ regenerating: false }"
    @submit="regenerating = true">

    @csrf

    <button
        type="submit"
        :disabled="regenerating"
        class="inline-flex items-center gap-2
               rounded-xl bg-indigo-600
               px-4 py-2.5 text-xs font-bold
               text-white transition-all
               hover:-translate-y-0.5
               hover:bg-indigo-500
               active:scale-95
               disabled:opacity-60">

        <span
            x-text="regenerating
                ? 'Regenerating...'
                : 'Regenerate Insight'">
        </span>

    </button>

</form>

@endsection



@section('content')

<div class="mx-auto max-w-6xl space-y-8">


    {{-- ====================================================== --}}
    {{-- EXAM IDENTITY                                          --}}
    {{-- ====================================================== --}}

    <section
        class="border-b border-slate-200 pb-7">

        <div
            class="flex flex-col gap-5
                   lg:flex-row lg:items-end
                   lg:justify-between">

            <div>

                <div class="flex items-center gap-2">

                    <span
                        class="flex h-7 w-7 items-center
                               justify-center rounded-lg
                               bg-indigo-600 text-[10px]
                               font-black text-white">

                        AI

                    </span>

                    <p
                        class="text-[9px] font-extrabold
                               uppercase tracking-[0.2em]
                               text-indigo-600">

                        Gemini-Assisted Analysis

                    </p>

                </div>


                <h1
                    class="mt-4 text-3xl font-black
                           tracking-[-0.05em]
                           text-slate-950">

                    {{ $exam->title }}

                </h1>


                <p
                    class="mt-2 text-sm text-slate-500">

                    {{ $exam->subject?->name ?? 'Unknown Subject' }}

                </p>

            </div>



            <div
                class="flex flex-wrap gap-x-7 gap-y-3">

                <div>

                    <p
                        class="text-[8px] font-extrabold
                               uppercase tracking-[0.16em]
                               text-slate-400">
                        Responses
                    </p>

                    <p
                        class="mt-1 text-sm font-black
                               text-slate-800">
                        {{ $analytics['total_attempts'] }}
                    </p>

                </div>


                <div>

                    <p
                        class="text-[8px] font-extrabold
                               uppercase tracking-[0.16em]
                               text-slate-400">
                        Average
                    </p>

                    <p
                        class="mt-1 text-sm font-black
                               text-slate-800">
                        {{ number_format($analytics['average_score'], 1) }}%
                    </p>

                </div>


                <div>

                    <p
                        class="text-[8px] font-extrabold
                               uppercase tracking-[0.16em]
                               text-slate-400">
                        Pass Rate
                    </p>

                    <p
                        class="mt-1 text-sm font-black
                               text-slate-800">
                        {{ number_format($analytics['pass_rate'], 1) }}%
                    </p>

                </div>

            </div>

        </div>

    </section>



    {{-- ====================================================== --}}
    {{-- AI PERFORMANCE SIGNAL                                  --}}
    {{-- ====================================================== --}}

    <section
        class="overflow-hidden rounded-xl
               bg-slate-950 text-white">

        <div
            class="grid lg:grid-cols-[220px_1fr]">

            <div
                class="border-b border-white/10
                       p-6 lg:border-b-0
                       lg:border-r">

                <p
                    class="text-[9px] font-extrabold
                           uppercase tracking-[0.2em]
                           text-indigo-300">

                    AI-Q Intelligence

                </p>


                <h2
                    class="mt-3 text-xl font-black
                           tracking-[-0.04em]">

                    Performance
                    Signal

                </h2>


                <p
                    class="mt-3 text-[11px]
                           leading-5 text-slate-400">

                    Gemini interpretation based
                    on calculated AI-Q examination
                    analytics.

                </p>

            </div>


            <div class="p-6 sm:p-8">

                <p
                    class="text-base font-medium
                           leading-8 text-slate-200">

                    {{ $aiInsight->performance_summary }}

                </p>

            </div>

        </div>

    </section>



    {{-- ====================================================== --}}
    {{-- FINDINGS / ACTIONS                                     --}}
    {{-- ====================================================== --}}

    <div
        class="grid gap-8
               lg:grid-cols-2">


        {{-- Findings --}}
        <section>

            <div
                class="flex items-center
                       justify-between
                       border-b border-slate-200
                       pb-3">

                <div>

                    <p
                        class="text-[9px] font-extrabold
                               uppercase tracking-[0.18em]
                               text-slate-400">

                        Evidence Interpretation

                    </p>

                    <h2
                        class="mt-1 text-lg font-black
                               tracking-[-0.03em]
                               text-slate-950">

                        Key Findings

                    </h2>

                </div>

            </div>


            <div class="divide-y divide-slate-100">

                @forelse(
                $aiInsight->key_findings ?? []
                as $finding
                )

                <div
                    class="group flex gap-4 py-5">

                    <div
                        class="mt-1 flex h-6 w-6
                                   shrink-0 items-center
                                   justify-center rounded-lg
                                   bg-indigo-50
                                   text-[9px] font-black
                                   text-indigo-600
                                   transition-transform
                                   group-hover:scale-110">

                        {{ $loop->iteration }}

                    </div>


                    <p
                        class="text-sm leading-6
                                   text-slate-600">

                        {{ $finding }}

                    </p>

                </div>

                @empty

                <p
                    class="py-6 text-sm
                               text-slate-400">

                    No findings were generated.

                </p>

                @endforelse

            </div>

        </section>



        {{-- Actions --}}
        <section>

            <div
                class="border-b border-slate-200
                       pb-3">

                <p
                    class="text-[9px] font-extrabold
                           uppercase tracking-[0.18em]
                           text-slate-400">

                    Instructional Direction

                </p>

                <h2
                    class="mt-1 text-lg font-black
                           tracking-[-0.03em]
                           text-slate-950">

                    Recommended Actions

                </h2>

            </div>


            <div class="divide-y divide-slate-100">

                @forelse(
                $aiInsight->recommended_actions ?? []
                as $action
                )

                <div
                    class="group flex gap-4 py-5">

                    <span
                        class="mt-1 text-sm font-black
                                   text-indigo-500
                                   transition-transform
                                   group-hover:translate-x-1">

                        →

                    </span>


                    <p
                        class="text-sm leading-6
                                   text-slate-600">

                        {{ $action }}

                    </p>

                </div>

                @empty

                <p
                    class="py-6 text-sm
                               text-slate-400">

                    No recommended actions were generated.

                </p>

                @endforelse

            </div>

        </section>

    </div>



    {{-- ====================================================== --}}
    {{-- EVIDENCE                                               --}}
    {{-- ====================================================== --}}

    <section
        class="border-t border-slate-200
               pt-6">

        <div
            class="flex flex-col gap-4
                   sm:flex-row sm:items-center
                   sm:justify-between">

            <div>

                <p
                    class="text-[9px] font-extrabold
                           uppercase tracking-[0.18em]
                           text-slate-400">

                    Based On

                </p>

                <p
                    class="mt-2 text-xs font-semibold
                           text-slate-600">

                    {{ $analytics['total_attempts'] }}
                    completed responses

                    ·

                    {{ count($analytics['question_analysis']) }}
                    analyzed questions

                    ·

                    {{ count($analytics['topic_analysis']) }}
                    analyzed topics

                </p>

            </div>


            <span
                class="inline-flex w-fit items-center
                       gap-2 rounded-full
                       bg-indigo-50 px-3 py-1.5
                       text-[9px] font-extrabold
                       uppercase tracking-[0.12em]
                       text-indigo-600">

                <span
                    class="h-1.5 w-1.5
                           rounded-full bg-indigo-500">
                </span>

                Gemini Generated

            </span>
            <div>
                @if($aiInsight->generated_at)

                <span
                    class="text-[10px]
               font-semibold
               text-slate-400">

                    Generated
                    {{ $aiInsight->generated_at->format('M d, Y · g:i A') }}

                </span>

                @endif
            </div>


        </div>

    </section>

</div>

@endsection