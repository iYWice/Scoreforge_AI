{-- Use the application layout and configure inherited page structure. --}
@extends('layouts.app')

{-- Define this page section and its presentation content. --}
@section('title', 'Create Exam')

{-- Define this page section and its presentation content. --}
@section('content')

<style>
    .aiq-exam-create { --aiq-purple:#7047df; --aiq-ink:#292437; --aiq-pink:#f4a8e9; --aiq-yellow:#ffe16a; color:var(--aiq-ink); }
    .exam-create-hero { position:relative; overflow:hidden; isolation:isolate; padding:20px; border:1.5px solid #33253f; border-radius:14px; background:linear-gradient(120deg,#ffe16a,#fff1b5 56%,#f2eaff); box-shadow:5px 6px 0 #33253f; }
    .exam-create-hero h1 { color:#292437 !important; letter-spacing:-.05em; }
    .exam-create-card { border:1.5px solid #33253f !important; border-radius:14px !important; background:#fffdf8 !important; box-shadow:6px 7px 0 #33253f,0 18px 45px #33253f14 !important; }
    .exam-create-card input:not([type="checkbox"]),.exam-create-card select { border:1px solid #ded5e7 !important; border-radius:9px !important; background:#fff !important; color:#292437 !important; }
    .exam-create-card input:focus,.exam-create-card select:focus { border-color:#9d81ed !important; box-shadow:0 0 0 4px #7047df1c !important; outline:none; }
    .exam-create-card label { color:#554b60 !important; }
    .exam-create-card input[type="checkbox"] { accent-color:#7047df; }
    .exam-create-card .bg-indigo-50 { border:1px solid #33253f; background:#f4a8e9 !important; color:#382d43 !important; box-shadow:2px 3px 0 #33253f; }
    .exam-create-card button[type="submit"] { border:1px solid #30243e; border-radius:9px !important; background:#7047df !important; box-shadow:0 5px 0 #30243e; }
    .exam-create-card button[type="submit"]:hover { background:#5936c0 !important; transform:translateY(-2px); box-shadow:0 7px 0 #30243e; }
    .aiq-exam-create .text-rose-600 { color:#9a3568 !important; }
    @media(max-width:640px){.exam-create-hero{padding:16px}.exam-create-card{padding:20px}}
    @media(prefers-reduced-motion:reduce){.aiq-exam-create *,.aiq-exam-create *::before,.aiq-exam-create *::after{transition-duration:.01ms!important;animation-duration:.01ms!important}}
</style>

<div class="aiq-exam-create mx-auto max-w-4xl space-y-6">
    <div class="exam-create-hero">
        <h1 class="text-2xl font-extrabold tracking-tight text-slate-950">Create New Exam</h1>
        <p class="mt-1 text-sm text-slate-500">Set up the exam parameters and select its class and subject.</p>
    </div>

    <div class="exam-create-card rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-8 aiq-panel aiq-panel-interactive">
        {{-- EXAM TITLE --}}
        <div class="mb-8 text-center">
            <div class="inline-flex items-center justify-center w-12 h-12 bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 rounded-xl mb-3 border border-indigo-100 dark:border-indigo-800/50 shadow-sm">
                {{-- Academic Assessment Icon --}}
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </div>
        </div>

        {-- Collect and submit the data for this operation. --}
        <form method="POST" action="{{ route('teacher.exams.store') }}" class="space-y-5">
            @csrf

            {{-- EXAM TITLE --}}
            <div>
                <label for="title" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Exam Title</label>
                <input
                    id="title"
                    type="text"
                    name="title"
                    value="{{ old('title') }}"
                    required
                    placeholder="e.g., Midterm Examination"
                    class="w-full rounded-xl border border-slate-300 bg-slate-50/50 px-4 py-2.5 text-slate-900 outline-none transition focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/30 aiq-field">
                @error('title')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- SUBJECT & CLASS GRID --}}
            @php
                $selectedClassId = old('class_id', '');
                $selectedSubjectId = old('subject_id', '');
            @endphp

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="class_id" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-600">
                        Target Class
                    </label>

                    <select
                        id="class_id"
                        name="class_id"
                        required
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 aiq-field">
                        <option value="">Select Class</option>
                        {-- Iterate over the related records and render each result. --}
                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}" @selected($selectedClassId == $class->id)>
                                {{ $class->name }}
                            </option>
                        @endforeach
                    </select>

                    @error('class_id')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="subject_id" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-600">
                        Subject
                    </label>

                    <select
                        id="subject_id"
                        name="subject_id"
                        required
                        class="w-full rounded-xl border border-slate-300 px-4 py-2.5 aiq-field">
                        <option value="">Select a class first</option>

                        {-- Iterate over the related records and render each result. --}
                        @foreach ($classes as $class)
                            {-- Iterate over the related records and render each result. --}
                            @foreach ($class->classSubjects as $offering)
                                <option
                                    value="{{ $offering->subject_id }}"
                                    data-class-id="{{ $class->id }}"
                                    @selected($selectedClassId == $class->id && $selectedSubjectId == $offering->subject_id)>
                                    {{ $offering->subject->name }} — EDP {{ $offering->edp_code }}
                                </option>
                            @endforeach
                        @endforeach
                    </select>

                    @error('subject_id')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- TOTAL ITEMS, DURATION & AUTOMATIC PASSING SCORE --}}
<div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
    <div>
        <label for="total_items"
               class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-600">
            Total Items
        </label>
        <input
            type="number"
            id="total_items"
            name="total_items"
            min="1"
            value="{{ old('total_items', 1) }}"
            required
            class="w-full rounded-xl border border-slate-300 px-4 py-2.5 aiq-field">
        @error('total_items')
            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="duration"
               class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-600">
            Duration (mins)
        </label>
        <input
            type="number"
            id="duration"
            name="duration"
            min="1"
            value="{{ old('duration') }}"
            required
            class="w-full rounded-xl border border-slate-300 px-4 py-2.5 aiq-field">
        @error('duration')
            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="passing_score"
               class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-600">
            Passing Score (75%)
        </label>
        <input
            type="number"
            id="passing_score"
            name="passing_score"
            value="{{ old('passing_score', 1) }}"
            readonly
            class="w-full rounded-xl border border-slate-300 bg-slate-100 px-4 py-2.5 aiq-field">
        <p class="mt-1 text-xs text-slate-500">
            Automatically calculated from total items.
        </p>
    </div>
</div>

            {{-- RANDOMIZED QUESTION ORDER --}}
            <label class="flex items-start gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 cursor-pointer aiq-option">
                <input
                    type="checkbox"
                    name="randomize_questions"
                    value="1"
                    @checked(old('randomize_questions', '1') == '1')
                    class="mt-0.5 h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 aiq-check">
                <span>
                    <span class="block text-sm font-semibold text-slate-800">Randomize question order</span>
                    <span class="mt-0.5 block text-xs text-slate-500">Each student receives the questions in a shuffled order.</span>
                </span>
            </label>

            {{-- ACTION BUTTON --}}
            <div class="pt-3">
                <button
                    type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-semibold py-3 px-4 rounded-xl shadow-md shadow-indigo-500/20 hover:shadow-lg hover:shadow-indigo-500/30 transition-all duration-200 ease-[cubic-bezier(0.34,1.56,0.64,1)] hover:scale-[1.02] active:scale-95 cursor-pointer text-center text-sm tracking-wide flex items-center justify-center gap-2 group aiq-button aiq-button-primary">
                    <span>Create Exam</span>
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform duration-200 ease-out" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const classSelect = document.getElementById('class_id');
    const subjectSelect = document.getElementById('subject_id');
    const totalItems = document.getElementById('total_items');
    const passingScore = document.getElementById('passing_score');

    if (classSelect && subjectSelect) {
        function filterSubjects(resetSelection = false) {
            const classId = classSelect.value;

            Array.from(subjectSelect.options).forEach(function (option, index) {
                if (index === 0) return;

                const belongsToClass = option.dataset.classId === classId;
                option.hidden = !belongsToClass;
                option.disabled = !belongsToClass;
            });

            subjectSelect.disabled = !classId;

            if (
                resetSelection ||
                !subjectSelect.selectedOptions.length ||
                subjectSelect.selectedOptions[0].disabled
            ) {
                subjectSelect.value = '';
            }
        }

        classSelect.addEventListener('change', function () {
            filterSubjects(true);
        });

        filterSubjects();
    }

    if (totalItems && passingScore) {
        function updatePassingScore() {
            const items = parseInt(totalItems.value, 10) || 0;
            passingScore.value = items > 0 ? Math.ceil(items * 0.75) : '';
        }

        totalItems.addEventListener('input', updatePassingScore);
        updatePassingScore();
    }
});
</script>
@endpush