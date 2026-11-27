{-- Use the application layout and configure inherited page structure. --}
@extends('layouts.app')

{-- Define this page section and its presentation content. --}
@section('title', 'Examinations')
{-- Define this page section and its presentation content. --}
@section('eyebrow', 'Examination Workspace')

{-- Define this page section and its presentation content. --}
@section('page-actions')
<button
    type="button"
    onclick="openCreateExam()"
    class="aiq-button aiq-button-primary group inline-flex items-center gap-2
           rounded-xl bg-slate-950 px-4 py-2.5
           text-xs font-bold text-white
           transition-all duration-200
           hover:-translate-y-0.5 hover:bg-indigo-600
           hover:shadow-lg hover:shadow-indigo-200
           active:translate-y-0 active:scale-95">

    <svg
        class="h-4 w-4 transition-transform duration-300
               group-hover:rotate-90 group-hover:scale-110"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24">
        <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2.3"
            d="M12 4v16m8-8H4" />
    </svg>

    Create Exam
</button>
@endsection


{-- Define this page section and its presentation content. --}
@section('content')

<style>
    .aiq-exams-index { --aiq-purple:#7047df; --aiq-ink:#292437; --aiq-pink:#f4a8e9; --aiq-yellow:#ffe16a; color:var(--aiq-ink); }
    .exam-index-hero { position:relative; overflow:hidden; isolation:isolate; padding:22px; border:1.5px solid #33253f; border-radius:14px; background:linear-gradient(120deg,#ffe16a,#fff1b5 55%,#f2eaff); box-shadow:5px 6px 0 #33253f; }
    .exam-index-hero h2 { color:#292437 !important; }
    .exam-index-hero p { color:#62576b !important; }
    .exam-index-search { border:1px solid #d9cfe7 !important; border-radius:9px !important; background:#fffdf8 !important; }
    .exam-index-search:focus { border-color:#9d81ed !important; box-shadow:0 0 0 4px #7047df1c !important; }
    .exam-status-tabs { border-color:#ded5e7 !important; }
    .exam-status-tabs button { transition:transform .18s,color .18s; }
    .exam-status-tabs button:hover { transform:translateY(-2px); color:#7047df !important; }
    .exam-status-tabs [x-show] { background:#ffe16a !important; }
    .exam-status-tabs .rounded-full { background:#f4a8e9 !important; color:#382d43 !important; }
    #createExamPanel { border:1.5px solid #33253f !important; border-radius:14px; background:#fffdf8 !important; box-shadow:7px 8px 0 #33253f,0 20px 45px #33253f18 !important; }
    #createExamPanel > div > div:first-child { background:linear-gradient(145deg,#7047df,#5230b5) !important; }
    #createExamPanel .text-indigo-400 { color:#ffe16a !important; }
    #createExamPanel input,#createExamPanel select { border-radius:8px; }
    #createExamPanel input:focus,#createExamPanel select:focus { border-color:#9d81ed; box-shadow:0 0 0 4px #7047df1c; }
    #createExamPanel button[type="submit"] { border:1px solid #30243e; border-radius:9px; background:#7047df !important; box-shadow:0 4px 0 #30243e; }
    #createExamPanel button[type="submit"]:hover { background:#5936c0 !important; transform:translateY(-2px); box-shadow:0 6px 0 #30243e; }
    .exam-index-list { overflow:hidden; border:1.5px solid #382d43; border-radius:12px; background:#fffdf8; box-shadow:0 4px 0 #382d43; }
    .exam-index-row { border-color:#e8e0eb !important; transition:transform .18s,background .18s,box-shadow .18s; }
    .exam-index-row:hover { transform:translateY(-2px); background:#fff8df !important; box-shadow:0 4px 12px #382d4312; }
    .exam-index-row h3 { color:#292437 !important; }
    .exam-index-row.menu-open { z-index:60 !important; }
    .exam-index-list:has(.exam-index-row.menu-open) { overflow:visible; }
    #aiq-page-content .exam-row-controls { display:flex; flex:0 1 auto; flex-wrap:wrap; align-items:center; gap:8px; min-width:0; }
    #aiq-page-content .exam-row-controls form { flex:0 0 auto; }
    #aiq-page-content .exam-row-controls > a { white-space:nowrap; }
    #aiq-page-content .exam-row-status { width:auto !important; min-width:108px; max-width:122px; flex:0 0 auto; }
    #aiq-page-content .exam-row-more-button { box-sizing:border-box; width:40px !important; min-width:40px !important; height:40px !important; min-height:40px !important; flex:0 0 40px !important; padding:0 !important; }
    .exam-index-empty { border:1.5px dashed #7047df; border-radius:12px; background:#fffdf8; }
    .aiq-exams-index input:not([type="checkbox"]),.aiq-exams-index select { border-radius:8px; }
    @media(max-width:640px){.exam-index-hero{padding:16px}}
    @media(prefers-reduced-motion:reduce){.aiq-exams-index *,.aiq-exams-index *::before,.aiq-exams-index *::after{transition-duration:.01ms!important;animation-duration:.01ms!important}}
</style>
@php
$publishedCount = $exams->where('status', 'published')->count();
$draftCount = $exams->where('status', 'draft')->count();
$closedCount = $exams->where('status', 'closed')->count();
@endphp


<div
    x-data="{
        filter: 'all',
        search: ''
    }"
    class="aiq-exams-index space-y-8">


    {{-- ====================================================== --}}
    {{-- WORKSPACE INTRO                                        --}}
    {{-- ====================================================== --}}

    {-- Render this meaningful page or content section. --}
    <section class="exam-index-hero">

        <div class="flex flex-col gap-5
                    lg:flex-row lg:items-end lg:justify-between">

            <div class="max-w-2xl">

                <p class="text-[10px] font-extrabold uppercase
                          tracking-[0.22em] text-indigo-600">
                    Assessment Management
                </p>

                <h2 class="mt-2 text-2xl font-extrabold
                           tracking-[-0.04em] text-slate-950">
                    Your examinations.
                </h2>

                <p class="mt-2 text-sm leading-6 text-slate-500">
                    Create assessments, manage questions, control
                    publishing and review examination performance.
                </p>

            </div>


            {{-- Search --}}
            <div class="relative w-full lg:w-80">

                <svg
                    class="absolute left-3.5 top-1/2 h-4 w-4
                           -translate-y-1/2 text-slate-400"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M21 21l-4.35-4.35m2.35-5.65a8 8 0 11-16 0 8 8 0 0116 0z" />
                </svg>

                <input
                    x-model="search"
                    type="text"
                    placeholder="Search examinations..."
                    class="exam-index-search w-full rounded-xl border border-slate-200
                           bg-white py-2.5 pl-10 pr-4
                           text-sm font-medium text-slate-700
                           outline-none transition-all
                           placeholder:text-slate-400
                           focus:border-indigo-400
                           focus:ring-4 focus:ring-indigo-100/60 aiq-field">

            </div>

        </div>

    </section>



    {{-- ====================================================== --}}
    {{-- STATUS FILTERS                                         --}}
    {{-- ====================================================== --}}

    {-- Render this meaningful page or content section. --}
    <section class="exam-status-tabs border-y border-slate-200">

        <div class="flex items-center gap-1 overflow-x-auto">

            <button
                type="button"
                @click="filter = 'all'"
                :class="filter === 'all'
                    ? 'text-indigo-700'
                    : 'text-slate-500 hover:text-slate-900'"
                class="group relative flex shrink-0
                       items-center gap-2 px-4 py-4
                       text-xs font-bold
                       transition-all active:scale-95">

                All

                <span
                    class="rounded-full bg-slate-100
                           px-2 py-0.5 text-[10px]">
                    {{ $exams->count() }}
                </span>

                <span
                    x-show="filter === 'all'"
                    class="absolute inset-x-3 bottom-0
                           h-[2px] bg-indigo-600">
                </span>

            </button>


            <button
                type="button"
                @click="filter = 'published'"
                :class="filter === 'published'
                    ? 'text-indigo-700'
                    : 'text-slate-500 hover:text-slate-900'"
                class="relative flex shrink-0
                       items-center gap-2 px-4 py-4
                       text-xs font-bold
                       transition-all active:scale-95">

                Published

                <span class="rounded-full bg-emerald-50
                             px-2 py-0.5 text-[10px]
                             text-emerald-700">
                    {{ $publishedCount }}
                </span>

                <span
                    x-show="filter === 'published'"
                    class="absolute inset-x-3 bottom-0
                           h-[2px] bg-indigo-600">
                </span>

            </button>


            <button
                type="button"
                @click="filter = 'draft'"
                :class="filter === 'draft'
                    ? 'text-indigo-700'
                    : 'text-slate-500 hover:text-slate-900'"
                class="relative flex shrink-0
                       items-center gap-2 px-4 py-4
                       text-xs font-bold
                       transition-all active:scale-95">

                Draft

                <span class="rounded-full bg-amber-50
                             px-2 py-0.5 text-[10px]
                             text-amber-700">
                    {{ $draftCount }}
                </span>

                <span
                    x-show="filter === 'draft'"
                    class="absolute inset-x-3 bottom-0
                           h-[2px] bg-indigo-600">
                </span>

            </button>


            <button
                type="button"
                @click="filter = 'closed'"
                :class="filter === 'closed'
                    ? 'text-indigo-700'
                    : 'text-slate-500 hover:text-slate-900'"
                class="relative flex shrink-0
                       items-center gap-2 px-4 py-4
                       text-xs font-bold
                       transition-all active:scale-95">

                Closed

                <span class="rounded-full bg-slate-100
                             px-2 py-0.5 text-[10px]
                             text-slate-600">
                    {{ $closedCount }}
                </span>

                <span
                    x-show="filter === 'closed'"
                    class="absolute inset-x-3 bottom-0
                           h-[2px] bg-indigo-600">
                </span>

            </button>

        </div>

    </section>



    {{-- ====================================================== --}}
    {{-- CREATE EXAM DRAWER                                     --}}
    {{-- ====================================================== --}}

    {-- Render this meaningful page or content section. --}
    <section
        id="createExamPanel"
        class="hidden overflow-hidden aiq-panel-interactive
               border border-indigo-100
               bg-white shadow-xl shadow-slate-200/40">

        <div class="grid lg:grid-cols-[280px_1fr]">


            {{-- Left introduction --}}
            <div class="relative overflow-hidden
                        bg-slate-950 p-7 text-white">

                <div class="relative z-10">

                    <span class="text-[10px] font-extrabold
                                 uppercase tracking-[0.2em]
                                 text-indigo-400">
                        New Assessment
                    </span>

                    <h3 class="mt-3 text-2xl font-extrabold
                               tracking-[-0.04em]">
                        Create an examination.
                    </h3>

                    <p class="mt-3 text-sm leading-6
                              text-slate-400">
                        Set the basic examination information.
                        Questions can be added after creation.
                    </p>


                    <div class="mt-8 space-y-4">

                        <div class="flex items-center gap-3
                                    text-xs text-slate-400">

                            <span class="flex h-6 w-6
                                         items-center justify-center
                                         rounded-full
                                         bg-indigo-500/15
                                         font-bold text-indigo-300">
                                1
                            </span>

                            Exam information

                        </div>

                        <div class="flex items-center gap-3
                                    text-xs text-slate-500">

                            <span class="flex h-6 w-6
                                         items-center justify-center
                                         rounded-full
                                         bg-white/5 font-bold">
                                2
                            </span>

                            Build questions

                        </div>

                        <div class="flex items-center gap-3
                                    text-xs text-slate-500">

                            <span class="flex h-6 w-6
                                         items-center justify-center
                                         rounded-full
                                         bg-white/5 font-bold">
                                3
                            </span>

                            Publish examination

                        </div>

                    </div>

                </div>


                <div class="absolute -bottom-24 -right-24
                            h-64 w-64 rounded-full
                            bg-indigo-600/20 blur-3xl">
                </div>

            </div>



            {{-- Form --}}
            <div class="p-6 sm:p-8">

                <div class="mb-7 flex items-start
                            justify-between gap-4">

                    <div>

                        <p class="text-[10px] font-extrabold
                                  uppercase tracking-[0.18em]
                                  text-slate-400">
                            Examination Information
                        </p>

                        <h3 class="mt-1 text-lg font-extrabold
                                   text-slate-950">
                            Basic configuration
                        </h3>

                    </div>


                    <button
                        type="button"
                        onclick="closeCreateExam()"
                        class="flex h-9 w-9
                               items-center justify-center
                               rounded-full text-slate-400
                               transition-all
                               hover:rotate-90
                               hover:bg-rose-50
                               hover:text-rose-600
                               active:scale-75 aiq-button aiq-button-danger">
                        ×
                    </button>

                </div>


                <div
                    id="examErrors"
                    class="mb-6 hidden border border-rose-200
                           bg-rose-50 px-4 py-3
                           text-sm font-medium text-rose-700 aiq-alert aiq-alert-error">
                </div>


                {-- Collect and submit the data for this operation. --}
                <form
                    id="createExamForm"
                    method="POST"
                    action="{{ route('teacher.exams.store') }}">

                    @csrf


                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">


                        {{-- Title --}}
                        <div class="md:col-span-2">

                            <label class="mb-2 block text-[10px]
                                          font-extrabold uppercase
                                          tracking-[0.16em]
                                          text-slate-500">
                                Examination Title
                            </label>

                            <input
                                type="text"
                                name="title"
                                required
                                placeholder="e.g. Database Systems Midterm Examination"
                                class="w-full rounded-xl
                                       border border-slate-200
                                       bg-slate-50 px-4 py-3
                                       text-sm font-semibold
                                       text-slate-800 outline-none
                                       transition-all
                                       placeholder:font-normal
                                       placeholder:text-slate-400
                                       hover:border-slate-300
                                       focus:border-indigo-500
                                       focus:bg-white
                                       focus:ring-4
                                       focus:ring-indigo-100 aiq-field">

                        </div>



                        {{-- Class --}}
<div>
    <label for="exam_class_id" class="mb-2 block text-[10px] font-extrabold uppercase tracking-[0.16em] text-slate-500">
        Class
    </label>

    <select
        id="exam_class_id"
        name="class_id"
        required
        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 aiq-field">
        <option value="">Select class</option>

        {-- Iterate over the related records and render each result. --}
        @foreach ($classes as $class)
            <option value="{{ $class->id }}">
                {{ $class->name }}
            </option>
        @endforeach
    </select>
</div>

{{-- Subject offered in selected class --}}
<div>
    <label for="exam_subject_id" class="mb-2 block text-[10px] font-extrabold uppercase tracking-[0.16em] text-slate-500">
        Subject
    </label>

    <select
        id="exam_subject_id"
        name="subject_id"
        required
        disabled
        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 disabled:cursor-not-allowed disabled:opacity-60 aiq-field">
        <option value="">Select a class first</option>

        {-- Iterate over the related records and render each result. --}
        @foreach ($classes as $class)
            {-- Iterate over the related records and render each result. --}
            @foreach ($class->classSubjects->unique('subject_id') as $offering)
                <option
                    value="{{ $offering->subject_id }}"
                    data-class-id="{{ $class->id }}">
                    {{ $offering->subject->name }}
                </option>
            @endforeach
        @endforeach
    </select>
</div>



                        {{-- Total Items --}}
                        <div>

                            <label class="mb-2 block text-[10px]
                                          font-extrabold uppercase
                                          tracking-[0.16em]
                                          text-slate-500">
                                Total Items
                            </label>

                            <input
                                type="number"
                                name="total_items"
                                min="1"
                                required
                                placeholder="20"
                                class="w-full rounded-xl
                                       border border-slate-200
                                       bg-slate-50 px-4 py-3
                                       text-sm font-semibold
                                       outline-none transition-all
                                       focus:border-indigo-500
                                       focus:bg-white
                                       focus:ring-4
                                       focus:ring-indigo-100 aiq-field">

                        </div>



                        {{-- Duration --}}
                        <div>

                            <label class="mb-2 block text-[10px]
                                          font-extrabold uppercase
                                          tracking-[0.16em]
                                          text-slate-500">
                                Duration
                                <span class="normal-case tracking-normal
                                             text-slate-400">
                                    (minutes)
                                </span>
                            </label>

                            <input
                                type="number"
                                name="duration"
                                min="1"
                                required
                                placeholder="60"
                                class="w-full rounded-xl
                                       border border-slate-200
                                       bg-slate-50 px-4 py-3
                                       text-sm font-semibold
                                       outline-none transition-all
                                       focus:border-indigo-500
                                       focus:bg-white
                                       focus:ring-4
                                       focus:ring-indigo-100 aiq-field">

                        </div>



                        {{-- Passing Score --}}
                        <div class="md:col-span-2">

                            <label class="mb-2 block text-[10px]
                                          font-extrabold uppercase
                                          tracking-[0.16em]
                                          text-slate-500">
                                Passing Score
                            </label>

                            <input
                                type="number"
                                name="passing_score"
                                min="0"
                                required
                                placeholder="15"
                                class="w-full rounded-xl
                                       border border-slate-200
                                       bg-slate-50 px-4 py-3
                                       text-sm font-semibold
                                       outline-none transition-all
                                       focus:border-indigo-500
                                       focus:bg-white
                                       focus:ring-4
                                       focus:ring-indigo-100 aiq-field">

                        </div>



                        {{-- Randomized Question Order --}}
                        <div class="md:col-span-2">
                            <label class="flex items-start gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 cursor-pointer aiq-option">
                                <input
                                    type="checkbox"
                                    name="randomize_questions"
                                    value="1"
                                    checked
                                    class="mt-0.5 h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 aiq-check">
                                <span>
                                    <span class="block text-sm font-semibold text-slate-800">Randomize question order</span>
                                    <span class="mt-0.5 block text-xs text-slate-500">Each student receives the questions in a shuffled order.</span>
                                </span>
                            </label>
                        </div>

                        {{-- Schedule --}}
                        <div>
                            <label for="exam_opens_at" class="mb-2 block text-[10px] font-extrabold uppercase tracking-[0.16em] text-slate-500">
                                Opens At (Optional)
                            </label>
                            <input
                                id="exam_opens_at"
                                type="datetime-local"
                                name="opens_at"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 aiq-field">
                        </div>

                        <div>
                            <label for="exam_closes_at" class="mb-2 block text-[10px] font-extrabold uppercase tracking-[0.16em] text-slate-500">
                                Deadline (Optional)
                            </label>
                            <input
                                id="exam_closes_at"
                                type="datetime-local"
                                name="closes_at"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 aiq-field">
                            <p class="mt-1 text-xs text-slate-400">Leave empty to keep the exam open permanently.</p>
                        </div>

                    </div>


                    <div class="mt-7 flex items-center
                                justify-end gap-3
                                border-t border-slate-100 pt-6">

                        <button
                            type="button"
                            onclick="closeCreateExam()"
                            class="rounded-xl px-4 py-2.5
                                   text-xs font-bold text-slate-500
                                   transition-all
                                   hover:bg-slate-100
                                   hover:text-slate-800
                                   active:scale-95 aiq-button aiq-button-secondary">
                            Cancel
                        </button>

                        <button
                            id="createExamButton"
                            type="submit"
                            class="group inline-flex items-center
                                   gap-2 rounded-xl
                                   bg-indigo-600 px-5 py-2.5
                                   text-xs font-bold text-white
                                   transition-all duration-200
                                   hover:-translate-y-0.5
                                   hover:bg-indigo-700
                                   hover:shadow-lg
                                   hover:shadow-indigo-200
                                   active:translate-y-0
                                   active:scale-95
                                   disabled:cursor-not-allowed
                                   disabled:opacity-60 aiq-button aiq-button-primary">

                            <span
                                class="transition-transform
                                       group-hover:rotate-90">
                                +
                            </span>

                            <span id="createExamButtonText">
                                Create Examination
                            </span>

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </section>



    {{-- ====================================================== --}}
    {{-- EXAMINATION WORKSPACE LIST                              --}}
    {{-- ====================================================== --}}

    {-- Render this meaningful page or content section. --}
    <section>

        <div class="mb-4 flex items-center justify-between">

            <p class="text-[10px] font-extrabold uppercase
                      tracking-[0.2em] text-slate-400">
                Examination Workspace
            </p>

            <p class="text-xs font-semibold text-slate-400">
                {{ $exams->count() }}
                {{ Str::plural('assessment', $exams->count()) }}
            </p>

        </div>


        <div class="exam-index-list border-t border-slate-200 aiq-panel">

            {-- Iterate over the related records and render each result. --}
            @forelse($exams as $exam)

            <article
                x-show="
                        (filter === 'all' || filter === '{{ $exam->status }}') &&
                        (
                            search === '' ||
                            '{{ strtolower(addslashes($exam->title)) }}'
                                .includes(search.toLowerCase()) ||
                            '{{ strtolower(addslashes($exam->exam_code)) }}'
                                .includes(search.toLowerCase())
                        )
                    "
                x-transition.opacity.duration.200ms
                class="exam-index-row group relative border-b
                           border-slate-200 bg-transparent
                           transition-all duration-300
                           hover:bg-white hover:z-30 focus-within:z-30 aiq-panel-interactive">

                <div
                    class="absolute bottom-0 left-0 top-0
                               w-[3px] scale-y-0 bg-indigo-600
                               transition-transform duration-300
                               group-hover:scale-y-100">
                </div>


                <div class="px-2 py-6 sm:px-5">

                    <div class="grid gap-6
                                    lg:grid-cols-[minmax(0,1fr)_auto]
                                    lg:items-center">


                        {{-- Main exam information --}}
                        <div class="min-w-0">

                            <div class="mb-3 flex flex-wrap
                                            items-center gap-2">

                                {{-- Status --}}
                                @if($exam->status === 'published')

                                <span
                                    class="inline-flex items-center
                                                   gap-1.5 text-[10px]
                                                   font-extrabold uppercase
                                                   tracking-[0.14em]
                                                   text-emerald-700">

                                    <span
                                        class="h-1.5 w-1.5
                                                       rounded-full
                                                       bg-emerald-500">
                                    </span>

                                    Published

                                </span>

                                {-- Render this content conditionally based on the current view state. --}
                                @elseif($exam->status === 'draft')

                                <span
                                    class="inline-flex items-center
                                                   gap-1.5 text-[10px]
                                                   font-extrabold uppercase
                                                   tracking-[0.14em]
                                                   text-amber-700">

                                    <span
                                        class="h-1.5 w-1.5
                                                       rounded-full
                                                       bg-amber-500">
                                    </span>

                                    Draft

                                </span>

                                {-- Render this content conditionally based on the current view state. --}
                                @else

                                <span
                                    class="inline-flex items-center
                                                   gap-1.5 text-[10px]
                                                   font-extrabold uppercase
                                                   tracking-[0.14em]
                                                   text-slate-500">

                                    <span
                                        class="h-1.5 w-1.5
                                                       rounded-full
                                                       bg-slate-400">
                                    </span>

                                    Closed

                                </span>

                                @endif


                                <span class="text-slate-300">•</span>


                                {{-- Exam Code --}}
                                <span
                                    class="font-mono text-[10px]
                                               font-bold tracking-wider
                                               text-indigo-600">
                                    {{ $exam->exam_code }}
                                </span>

                                <span
                                    class="inline-flex items-center gap-1.5 rounded-full
                                           px-2.5 py-1 text-[9px] font-bold
                                           {{ $exam->randomize_questions
                                               ? 'bg-violet-50 text-violet-700'
                                               : 'bg-slate-100 text-slate-500' }}">
                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 3h5v5M4 20L21 3M21 16v5h-5M15 15l6 6M4 4l5 5" />
                                    </svg>
                                    {{ $exam->randomize_questions ? 'Randomized' : 'Fixed order' }}
                                </span>
                            </div>


                            <a
                                href="{{ route('teacher.exams.show', $exam->id) }}"
                                class="inline-block max-w-full
                                           transition-all duration-200
                                           active:scale-[0.98]">

                                <h3
                                    class="truncate text-lg
                                               font-extrabold
                                               tracking-[-0.025em]
                                               text-slate-950
                                               transition-colors
                                               group-hover:text-indigo-600
                                               sm:text-xl">
                                    {{ $exam->title }}
                                </h3>

                            </a>


                            <div
                                class="mt-4 flex flex-wrap
                                           items-center gap-x-5 gap-y-2
                                           text-xs font-medium
                                           text-slate-500">

                                <span>
                                    <strong class="font-bold
                                                       text-slate-700">
                                        {{ $exam->questions_count }}
                                    </strong>
                                    questions
                                </span>

                                <span class="hidden text-slate-300 sm:inline">
                                    /
                                </span>

                                <span>
                                    <strong class="font-bold
                                                       text-slate-700">
                                        {{ $exam->duration }}
                                    </strong>
                                    minutes
                                </span>

                                {-- Render this content conditionally based on the current view state. --}
                                @if($exam->subject)
                                <span class="hidden text-slate-300 sm:inline">
                                    /
                                </span>

                                <span>
                                    {{ $exam->subject->name }}
                                </span>
                                @endif

                                {-- Render this content conditionally based on the current view state. --}
                                @if($exam->class)
                                <span class="hidden text-slate-300 sm:inline">
                                    /
                                </span>

                                <span>
                                    {{ $exam->class->name }}
                                </span>
                                @endif

                            </div>

                        </div>



                        {{-- Controls --}}
                        <div class="flex flex-wrap items-center gap-2 exam-row-controls">


                            {{-- Status --}}
                            <form
                                method="POST"
                                action="{{ route('teacher.exams.status', $exam->id) }}">

                                @csrf
                                @method('PATCH')

                                <select
                                    name="status"
                                    onchange="this.form.submit()"
                                    title="Change examination status"
                                    class="cursor-pointer rounded-xl
                                               border border-slate-200
                                               bg-white px-3 py-2.5
                                               text-[10px] font-extrabold
                                               uppercase tracking-wider
                                               text-slate-600 outline-none
                                               transition-all
                                               hover:border-indigo-300
                                               hover:text-indigo-600
                                               focus:border-indigo-500
                                               focus:ring-4
                                               focus:ring-indigo-100 aiq-field exam-row-status">

                                    <option
                                        value="draft"
                                        {{ $exam->status === 'draft' ? 'selected' : '' }}>
                                        Disabled
                                    </option>

                                    <option
                                        value="published"
                                        {{ $exam->status === 'published' ? 'selected' : '' }}>
                                        Published
                                    </option>

                                    <option
                                        value="closed"
                                        {{ $exam->status === 'closed' ? 'selected' : '' }}>
                                        Closed
                                    </option>

                                </select>

                            </form>



                            {{-- Questions --}}
                            <a
                                href="{{ route('teacher.exams.questions', $exam->id) }}"
                                class="group/button inline-flex
                                           items-center gap-2
                                           rounded-xl border
                                           border-slate-200 bg-white
                                           px-3.5 py-2.5
                                           text-xs font-bold
                                           text-slate-700
                                           transition-all duration-200
                                           hover:-translate-y-0.5
                                           hover:border-indigo-300
                                           hover:text-indigo-600
                                           hover:shadow-md
                                           active:translate-y-0
                                           active:scale-95 aiq-button aiq-button-secondary">

                                Questions

                                <span
                                    class="transition-transform
                                               group-hover/button:translate-x-0.5">
                                    →
                                </span>

                            </a>



                            {{-- Analytics --}}
                            <a
                                href="{{ route('teacher.analytics.exam', $exam->id) }}"
                                class="group/button inline-flex
                                           items-center gap-2
                                           rounded-xl bg-slate-950
                                           px-3.5 py-2.5
                                           text-xs font-bold text-white
                                           transition-all duration-200
                                           hover:-translate-y-0.5
                                           hover:bg-indigo-600
                                           hover:shadow-lg
                                           hover:shadow-indigo-100
                                           active:translate-y-0
                                           active:scale-95 aiq-button aiq-button-primary">

                                Analyze

                                <span
                                    class="transition-transform
                                               group-hover/button:translate-x-1">
                                    ↗
                                </span>

                            </a>



                            {{-- More menu --}}
                            <div
                                x-data="{ open: false }"
                                x-effect="$el.closest('.exam-index-row')?.classList.toggle('menu-open', open)"
                                class="relative">

                                <button
                                    type="button"
                                    @click="open = !open"
                                    @click.outside="open = false"
                                    class="flex h-10 w-10
                                               items-center justify-center
                                               rounded-xl border
                                               border-slate-200 bg-white
                                               font-bold text-slate-500
                                               transition-all
                                               hover:border-slate-300
                                               hover:bg-slate-50
                                               hover:text-slate-900
                                               active:scale-90 aiq-button aiq-button-secondary exam-row-more-button">
                                    •••
                                </button>


                                <div
                                    x-cloak
                                    x-show="open"
                                    x-transition:enter="transition ease-out duration-150"
                                    x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                    x-transition:leave="transition ease-in duration-100"
                                    x-transition:leave-start="opacity-100 scale-100"
                                    x-transition:leave-end="opacity-0 scale-95"
                                    class="absolute right-0 z-30
                                               mt-2 w-48 overflow-hidden
                                               rounded-xl border
                                               border-slate-200
                                               bg-white p-1.5 aiq-panel">


                                    <a
                                        href="{{ route('teacher.exams.show', $exam->id) }}"
                                        class="flex items-center
                                                   rounded-lg px-3 py-2.5
                                                   text-xs font-semibold
                                                   text-slate-600
                                                   transition-all
                                                   hover:bg-slate-100
                                                   hover:text-slate-950">
                                        View details
                                    </a>


                                    <a
                                        href="{{ route('teacher.exams.edit', $exam->id) }}"
                                        class="flex items-center rounded-lg px-3 py-2.5 text-xs font-semibold text-slate-600 transition-all hover:bg-slate-100 hover:text-slate-950">
                                        Edit examination
                                    </a>


                                    <div class="my-1 border-t
                                                    border-slate-100">
                                    </div>


                                    {-- Collect and submit the data for this operation. --}
                                    <form
                                        action="{{ route('teacher.exams.destroy', $exam->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Delete this examination? This action cannot be undone.')">

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="flex w-full
                                                       items-center
                                                       rounded-lg
                                                       px-3 py-2.5
                                                       text-left text-xs
                                                       font-semibold
                                                       text-rose-600
                                                       transition-all
                                                       hover:bg-rose-50 aiq-button aiq-button-danger">
                                            Delete examination
                                        </button>

                                    </form>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </article>

            @empty

            <div class="exam-index-empty py-20 text-center">

                <div
                    class="mx-auto flex h-14 w-14
                               items-center justify-center
                               rounded-full bg-indigo-50
                               text-xl text-indigo-600">
                    +
                </div>

                <h3
                    class="mt-5 text-lg font-extrabold
                               text-slate-950">
                    No examinations yet.
                </h3>

                <p
                    class="mx-auto mt-2 max-w-sm
                               text-sm leading-6 text-slate-500">
                    Create your first examination and start
                    building its question bank.
                </p>

                <button
                    type="button"
                    onclick="openCreateExam()"
                    class="mt-6 rounded-xl bg-indigo-600
                               px-5 py-2.5 text-xs font-bold
                               text-white transition-all
                               hover:-translate-y-0.5
                               hover:bg-indigo-700
                               hover:shadow-lg
                               active:scale-95 aiq-button aiq-button-primary">
                    Create Examination
                </button>

            </div>

            @endforelse

        </div>


        {{-- No search result --}}
        <div
            x-show="
                search !== '' &&
                !$el.previousElementSibling.innerText
                    .toLowerCase()
                    .includes(search.toLowerCase())
            "
            class="hidden py-10 text-center
                   text-sm text-slate-400">
        </div>

    </section>

</div>


{{-- ========================================================== --}}
{{-- PAGE SCRIPT                                                --}}
{{-- ========================================================== --}}

<script>
    function openCreateExam() {

        const panel = document.getElementById('createExamPanel');

        if (!panel) {
            return;
        }

        panel.classList.remove('hidden');

        requestAnimationFrame(() => {
            panel.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        });

    }


    function closeCreateExam() {

        const panel = document.getElementById('createExamPanel');

        if (!panel) {
            return;
        }

        panel.classList.add('hidden');

    }

const examClassSelect = document.getElementById('exam_class_id');
const examSubjectSelect = document.getElementById('exam_subject_id');

if (examClassSelect && examSubjectSelect) {
    function filterExamSubjects(resetSelection = false) {
        const selectedClassId = examClassSelect.value;

        Array.from(examSubjectSelect.options).forEach(function (option, index) {
            if (index === 0) return;

            option.hidden = option.dataset.classId !== selectedClassId;
            option.disabled = option.dataset.classId !== selectedClassId;
        });

        examSubjectSelect.disabled = !selectedClassId;

        if (
            resetSelection ||
            examSubjectSelect.selectedOptions.length === 0 ||
            examSubjectSelect.selectedOptions[0].disabled
        ) {
            examSubjectSelect.value = '';
        }
    }

    examClassSelect.addEventListener('change', function () {
        filterExamSubjects(true);
    });

    filterExamSubjects();
}
    const createExamForm = document.getElementById('createExamForm');


    if (createExamForm) {

        createExamForm.addEventListener('submit', async function(event) {

            event.preventDefault();

            const form = this;
            const button = document.getElementById('createExamButton');
            const buttonText =
                document.getElementById('createExamButtonText');

            const errors =
                document.getElementById('examErrors');


            errors.classList.add('hidden');
            errors.innerHTML = '';

            button.disabled = true;
            buttonText.textContent = 'Creating...';


            try {

                const response = await fetch(form.action, {

                    method: 'POST',

                    headers: {
                        'X-CSRF-TOKEN': form.querySelector(
                            'input[name="_token"]'
                        ).value,

                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },

                    body: new FormData(form)

                });


                let data = {};

                try {
                    data = await response.json();
                } catch (error) {
                    data = {};
                }


                if (!response.ok) {

                    if (
                        response.status === 422 &&
                        data.errors
                    ) {

                        let message =
                            '<ul class="list-disc list-inside space-y-1">';

                        Object.values(data.errors)
                            .forEach(messages => {

                                messages.forEach(messageText => {

                                    message +=
                                        `<li>${messageText}</li>`;

                                });

                            });

                        message += '</ul>';

                        errors.innerHTML = message;

                    } else if (response.status === 419) {

                        errors.textContent =
                            'Your session expired. Refresh the page and try again.';

                    } else if (response.status === 403) {

                        errors.textContent =
                            'You are not authorized to create this examination.';

                    } else {

                        errors.textContent =
                            data.message ??
                            'Unable to create the examination.';

                    }


                    errors.classList.remove('hidden');

                    errors.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });

                    return;

                }


                /*
                 * Keep the same behavior you already had:
                 * creation happens without navigating away,
                 * then the workspace refreshes.
                 */
                window.location.reload();


            } catch (error) {

                console.error(error);

                errors.textContent =
                    'Something went wrong while creating the examination.';

                errors.classList.remove('hidden');


            } finally {

                button.disabled = false;
                buttonText.textContent = 'Create Examination';

            }

        });

    }
</script>

@endsection