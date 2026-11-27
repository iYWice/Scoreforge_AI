{-- Use the application layout and configure inherited page structure. --}
@extends('layouts.app')

{-- Define this page section and its presentation content. --}
@section('title', 'AI-Q Insight')

{-- Define this page section and its presentation content. --}
@section('eyebrow', 'AI-Assisted Examination Analysis')

{-- Define this page section and its presentation content. --}
@section('page-header')
AI-Q Insight
@endsection


{-- Define this page section and its presentation content. --}
@section('page-actions')

<a
    href="{{ route('teacher.analytics.exam', $exam) }}"
    class="aiq-button aiq-button-secondary inline-flex items-center gap-2 rounded-xl
               border border-slate-200 bg-white
               px-4 py-2.5 text-xs font-bold
               text-slate-600 transition-all
               hover:-translate-y-0.5
               hover:border-indigo-300
               hover:text-indigo-600
               active:scale-95">

    ← Back to Analysis

</a>

{-- Collect and submit the data for this operation. --}
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
               disabled:opacity-60 aiq-button aiq-button-primary">

        <span
            x-text="regenerating
                ? 'Regenerating...'
                : 'Regenerate Insight'">
        </span>

    </button>

</form>

@endsection



{-- Define this page section and its presentation content. --}
@section('content')

<style>
    .aiq-insight-page { --aiq-purple:#7047df; --aiq-ink:#292437; --aiq-pink:#f4a8e9; --aiq-yellow:#ffe16a; color:var(--aiq-ink); }
    .insight-hero { border:1.5px solid #33253f !important; border-radius:14px; padding:22px; background:linear-gradient(120deg,#ffe16a,#fff1b5 55%,#f2eaff); box-shadow:5px 6px 0 #33253f; }
    .insight-hero h1 { color:#292437 !important; }
    .insight-hero .bg-indigo-600 { border:1px solid #33253f; background:#7047df !important; box-shadow:2px 3px 0 #33253f; }
    .insight-signal { overflow:hidden; border:1.5px solid #30243e !important; border-radius:14px !important; background:linear-gradient(145deg,#7047df,#5230b5) !important; box-shadow:6px 7px 0 #30243e; }
    .insight-signal h2 { color:#fff !important; }
    .insight-signal .text-indigo-300 { color:#ffe16a !important; }
    .insight-signal .text-slate-400,.insight-signal .text-slate-200 { color:#e4dbff !important; }
    .insight-findings { border:1.5px solid #382d43; border-radius:12px; padding:18px; background:#fffdf8; box-shadow:0 4px 0 #382d43; }
    .insight-findings .bg-indigo-50 { border:1px solid #382d4330; background:#f4a8e9 !important; color:#35263f !important; }
    .insight-evidence { border:1.5px solid #33253f; border-radius:11px; padding:15px; background:#fff; box-shadow:0 4px 0 #33253f; }
    .aiq-insight-page button[type="submit"] { border:1px solid #30243e; border-radius:9px; background:#7047df !important; box-shadow:0 4px 0 #30243e; }
    .aiq-insight-page button[type="submit"]:hover { background:#5936c0 !important; transform:translateY(-2px); box-shadow:0 6px 0 #30243e; }
    @media(max-width:640px){.insight-hero{padding:16px}}
    @media(prefers-reduced-motion:reduce){.aiq-insight-page *,.aiq-insight-page *::before,.aiq-insight-page *::after{transition-duration:.01ms!important;animation-duration:.01ms!important}}
</style>
<div class="aiq-insight-page mx-auto max-w-6xl space-y-8">


    {{-- ====================================================== --}}
    {{-- EXAM IDENTITY                                          --}}
    {{-- ====================================================== --}}

    {-- Render this meaningful page or content section. --}
    <section
        class="insight-hero border-b border-slate-200 pb-7">

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

    {-- Render this meaningful page or content section. --}
    <section
        class="insight-signal overflow-hidden rounded-xl
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
        <section class="insight-findings aiq-panel">

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

                {-- Iterate over the related records and render each result. --}
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
        <section class="insight-findings aiq-panel">

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

                {-- Iterate over the related records and render each result. --}
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

    {-- Render this meaningful page or content section. --}
    <section
        class="insight-evidence border-t border-slate-200
               pt-6 aiq-panel">

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
                       text-indigo-600 aiq-badge">

                <span
                    class="h-1.5 w-1.5
                           rounded-full bg-indigo-500">
                </span>

                Gemini Generated

            </span>
            <div>
                {-- Render this content conditionally based on the current view state. --}
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