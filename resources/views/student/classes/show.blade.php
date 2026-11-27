{-- Use the application layout and configure inherited page structure. --}
@extends('layouts.app')

{-- Define this page section and its presentation content. --}
@section('title', $class->name)
{-- Define this page section and its presentation content. --}
@section('eyebrow', 'Class Workspace')

{-- Define this page section and its presentation content. --}
@section('content')

<div class="space-y-8 aiq-class-details">

    <a
        href="{{ route('student.classes.index') }}"
        class="inline-flex items-center gap-2
               text-xs font-bold text-slate-400
               transition hover:text-indigo-600 aiq-button aiq-button-secondary">
        ← My Classes
    </a>


    {{-- Header --}}
    <section
        class="flex flex-col gap-6
               border-b border-slate-200 pb-7
               lg:flex-row lg:items-end lg:justify-between aiq-panel aiq-panel-interactive p-5 sm:p-6">

        <div>
            <p class="text-[10px] font-extrabold uppercase
                      tracking-[0.2em] text-indigo-600">
                {{ $class->subject?->name ?? 'Subject' }}
            </p>

            <h2 class="mt-2 text-3xl font-black
                       tracking-[-0.05em] text-slate-950">
                {{ $class->name }}
            </h2>

            <p class="mt-2 text-sm text-slate-500">
                Teacher:
                <span class="font-semibold text-slate-700">
                    {{ $class->teacher?->fname }}
                    {{ $class->teacher?->lname }}
                </span>
            </p>
        </div>


        <div>
            <p class="text-[10px] font-bold uppercase
                      tracking-wider text-slate-400">
                Class Code
            </p>

            <p class="mt-1 inline-flex rounded-xl bg-indigo-50 px-3 py-2 font-mono text-lg font-black
                      tracking-[0.18em] text-indigo-800 aiq-badge">
                {{ $class->class_code }}
            </p>
        </div>

    </section>

{-- Render this meaningful page or content section. --}
<section>
    <h3 class="text-xl font-extrabold text-slate-950">My Subjects</h3>
    <p class="mt-1 text-sm text-slate-500">
        Subjects your teacher assigned to you in this class.
    </p>

    <div class="mt-5 grid gap-4 md:grid-cols-2">
        {-- Iterate over the related records and render each result. --}
        @forelse($class->classSubjects as $offering)
            <article class="rounded-xl border border-slate-200 bg-white p-5 aiq-panel aiq-panel-interactive">
                <h4 class="font-bold text-slate-900">
                    {{ $offering->subject->name }}
                </h4>
                <p class="mt-2 text-sm text-slate-600">
                    EDP Code: <span class="font-mono">{{ $offering->edp_code }}</span>
                </p>
                <p class="mt-1 text-sm text-slate-600">
                    Schedule: {{ $offering->schedule }}
                </p>
            </article>
        @empty
            <p class="rounded-xl border border-dashed border-slate-300 bg-white p-5 text-sm text-slate-500 aiq-panel">
                Your teacher has not assigned you to a subject yet.
            </p>
        @endforelse
    </div>
</section>
    {{-- Exams --}}
    <section>

        <div>
            <p class="text-[10px] font-extrabold uppercase
                      tracking-[0.2em] text-indigo-600">
                Assessments
            </p>

            <h3 class="mt-2 text-xl font-extrabold text-slate-950">
                Published Examinations
            </h3>

            <p class="mt-1 text-sm text-slate-500">
                Examinations currently available for this class.
            </p>
        </div>


        <div class="mt-6 grid gap-4">

            {-- Iterate over the related records and render each result. --}
            @forelse($class->exams as $exam)

            <div
                class="flex flex-col gap-4 rounded-xl border border-slate-200 bg-white p-4 aiq-panel aiq-panel-interactive
                           sm:flex-row sm:items-center
                           sm:justify-between">

                <div>

                    <div class="flex flex-wrap items-center gap-3">

                        <h4 class="text-sm font-bold text-slate-900">
                            {{ $exam->title }}
                        </h4>

                        {-- Render this content conditionally based on the current view state. --}
                        @if($exam->opens_at && now()->lt($exam->opens_at))
                            <span
                                class="rounded-full bg-amber-50
                                           px-2 py-1
                                           text-[9px] font-extrabold
                                           uppercase tracking-wider
                                           text-amber-700 aiq-badge">
                                Upcoming
                            </span>
                        {-- Render this content conditionally based on the current view state. --}
                        @else
                            <span
                                class="rounded-full bg-emerald-50
                                           px-2 py-1
                                           text-[9px] font-extrabold
                                           uppercase tracking-wider
                                           text-emerald-600 aiq-badge">
                                Open
                            </span>
                        @endif

                    </div>

                    <p class="mt-2 text-xs text-slate-400">
                        Exam Code:
                        <span class="font-mono font-bold text-slate-600">
                            {{ $exam->exam_code }}
                        </span>
                    </p>

                    <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-[11px] font-medium text-slate-500">
                        {-- Render this content conditionally based on the current view state. --}
                        @if($exam->opens_at)
                            <span>Opens: {{ $exam->opens_at->format('M j, Y · g:i A') }}</span>
                        {-- Render this content conditionally based on the current view state. --}
                        @else
                            <span>Opens: Immediately</span>
                        @endif

                        {-- Render this content conditionally based on the current view state. --}
                        @if($exam->closes_at)
                            <span>Deadline: {{ $exam->closes_at->format('M j, Y · g:i A') }}</span>
                        {-- Render this content conditionally based on the current view state. --}
                        @else
                            <span>No deadline</span>
                        @endif
                    </div>
                </div>


                {-- Collect and submit the data for this operation. --}
                <form
                    method="POST"
                    action="{{ route('student.exam.start') }}">
                    @csrf

                    <input
                        type="hidden"
                        name="exam_code"
                        value="{{ $exam->exam_code }}">

                    <button
                        type="submit"
                        class="rounded-xl bg-slate-950
                                   px-4 py-2.5
                                   text-xs font-bold text-white
                                   transition-all
                                   hover:bg-indigo-600
                                   active:scale-95 aiq-button aiq-button-primary">
                        Open Exam →
                    </button>
                </form>

            </div>

            @empty

            <div class="py-14 text-center aiq-panel">

                <p class="text-sm font-bold text-slate-700">
                    No published examinations
                </p>

                <p class="mt-2 text-sm text-slate-400">
                    Your teacher has not published an examination
                    for this class yet.
                </p>

            </div>

            @endforelse

        </div>

    </section>

</div>

@endsection