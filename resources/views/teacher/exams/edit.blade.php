{-- Use the application layout and configure inherited page structure. --}
@extends('layouts.app')

{-- Define this page section and its presentation content. --}
@section('title', 'Edit Exam')

{-- Define this page section and its presentation content. --}
@section('content')

<style>
    .aiq-exam-edit { --aiq-purple:#7047df; --aiq-ink:#292437; --aiq-pink:#f4a8e9; --aiq-yellow:#ffe16a; color:var(--aiq-ink); }
    .exam-edit-hero { position:relative; overflow:hidden; isolation:isolate; padding:23px; border:1.5px solid #33253f; border-radius:14px; background:linear-gradient(120deg,#ffe16a,#fff1b5 56%,#f2eaff); box-shadow:5px 6px 0 #33253f; }
    .exam-edit-hero h1 { color:#292437 !important; letter-spacing:-.05em; }
    .exam-edit-card { border:1.5px solid #33253f !important; border-radius:14px !important; background:#fffdf8 !important; box-shadow:6px 7px 0 #33253f,0 18px 45px #33253f14 !important; }
    .exam-edit-card input:not([type="checkbox"]),.exam-edit-card select { border:1px solid #ded5e7 !important; border-radius:9px !important; background:#fff !important; color:#292437 !important; }
    .exam-edit-card input:focus,.exam-edit-card select:focus { border-color:#9d81ed !important; box-shadow:0 0 0 4px #7047df1c !important; outline:none; }
    .exam-edit-card label { color:#554b60 !important; }
    .exam-edit-card .border-t { border-color:#e8e0eb !important; }
    .exam-edit-card button[type="submit"] { border:1px solid #30243e; border-radius:9px; background:#7047df !important; box-shadow:0 4px 0 #30243e; }
    .exam-edit-card button[type="submit"]:hover { background:#5936c0 !important; transform:translateY(-2px); box-shadow:0 6px 0 #30243e; }
    .exam-edit-back { display:inline-flex; padding:7px 10px; border-radius:8px; background:#f1eaff; color:#5937bd !important; }
    .exam-edit-back:hover { background:#f4a8e9; }
    .exam-edit-card input[type="checkbox"] { accent-color:#7047df; }
    @media(max-width:640px){.exam-edit-hero{padding:16px}.exam-edit-card{padding:20px}}
    @media(prefers-reduced-motion:reduce){.aiq-exam-edit *,.aiq-exam-edit *::before,.aiq-exam-edit *::after{transition-duration:.01ms!important;animation-duration:.01ms!important}}
</style>


<div class="aiq-exam-edit max-w-3xl mx-auto space-y-6">

    {{-- Breadcrumb & Back Action --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('teacher.exams.index') }}" class="exam-edit-back inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-indigo-600 transition-colors aiq-button aiq-button-secondary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back to Exams
        </a>
        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Exam Settings</span>
    </div>

    {{-- Form Card Container --}}
    <div class="exam-edit-card bg-white shadow-xl shadow-slate-900/5 rounded-2xl border border-slate-200/80 p-6 sm:p-8 transition-colors aiq-panel aiq-panel-interactive">

        <div class="exam-edit-hero mb-6 pb-6 border-b border-slate-100">
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">
                Edit Exam Configurations
            </h1>
            <p class="text-xs font-medium text-slate-400 mt-1">
                Update assessment metadata, target audience, and duration settings.
            </p>
        </div>

        {-- Collect and submit the data for this operation. --}
        <form method="POST" action="{{ route('teacher.exams.update', $exam->id) }}" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Exam Title --}}
            <div class="space-y-1.5">
                <label for="title" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                    Exam Title
                </label>
                <input
                    type="text"
                    id="title"
                    name="title"
                    value="{{ old('title', $exam->title) }}"
                    required
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs font-medium text-slate-800 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 focus:outline-none transition-all placeholder:text-slate-400 aiq-field"
                    placeholder="e.g. Midterm Comprehensive Assessment">
                @error('title')
                <p class="text-[11px] font-semibold text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Subject & Class Grid --}}
            @php
    $selectedClassId = old('class_id', isset($exam) ? $exam->class_id : '');
    $selectedSubjectId = old('subject_id', isset($exam) ? $exam->subject_id : '');
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
                <option
                    value="{{ $class->id }}"
                    @selected($selectedClassId == $class->id)>
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
                        @selected(
                            $selectedClassId == $class->id &&
                            $selectedSubjectId == $offering->subject_id
                        )>
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const classSelect = document.getElementById('class_id');
    const subjectSelect = document.getElementById('subject_id');

    if (!classSelect || !subjectSelect) return;

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
});
</script>

            {{-- TOTAL ITEMS, DURATION & AUTOMATIC PASSING SCORE --}}
<div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
    <div class="space-y-1.5">
        <label for="total_items"
               class="block text-xs font-bold uppercase tracking-wider text-slate-700">
            Total Items
        </label>
        <input
            type="number"
            id="total_items"
            name="total_items"
            value="{{ old('total_items', $exam->total_items) }}"
            min="1"
            required
            class="w-full rounded-xl border border-slate-200 px-4 py-3 aiq-field">
        @error('total_items')
            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="space-y-1.5">
        <label for="duration"
               class="block text-xs font-bold uppercase tracking-wider text-slate-700">
            Duration (Minutes)
        </label>
        <input
            type="number"
            id="duration"
            name="duration"
            value="{{ old('duration', $exam->duration) }}"
            min="1"
            required
            class="w-full rounded-xl border border-slate-200 px-4 py-3 aiq-field">
        @error('duration')
            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="space-y-1.5">
        <label for="passing_score"
               class="block text-xs font-bold uppercase tracking-wider text-slate-700">
            Passing Score (75%)
        </label>
        <input
            type="number"
            id="passing_score"
            name="passing_score"
            value="{{ old('passing_score', (int) ceil($exam->total_items * 0.75)) }}"
            readonly
            class="w-full rounded-xl border border-slate-200 bg-slate-100 px-4 py-3 aiq-field">
        <p class="mt-1 text-xs text-slate-500">
            Automatically calculated from total items.
        </p>
    </div>
</div>

            {{-- Schedule --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="space-y-1.5">
                    <label for="opens_at" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                        Opens At (Optional)
                    </label>
                    <input
                        type="datetime-local"
                        id="opens_at"
                        name="opens_at"
                        value="{{ old('opens_at', $exam->opens_at?->format('Y-m-d\TH:i')) }}"
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 aiq-field">
                    @error('opens_at')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <label for="closes_at" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                        Deadline (Optional)
                    </label>
                    <input
                        type="datetime-local"
                        id="closes_at"
                        name="closes_at"
                        value="{{ old('closes_at', $exam->closes_at?->format('Y-m-d\TH:i')) }}"
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 aiq-field">
                    <p class="mt-1 text-xs text-slate-500">Leave empty to keep the exam open permanently.</p>
                    @error('closes_at')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- RANDOMIZED QUESTION ORDER --}}
            <label class="flex items-start gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 cursor-pointer aiq-option">
                <input
                    type="checkbox"
                    name="randomize_questions"
                    value="1"
                    @checked(old('randomize_questions', $exam->randomize_questions))
                    class="mt-0.5 h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 aiq-check">
                <span>
                    <span class="block text-sm font-semibold text-slate-800">Randomize question order</span>
                    <span class="mt-0.5 block text-xs text-slate-500">Each student receives the questions in a shuffled order.</span>
                </span>
            </label>

            {{-- Action Buttons --}}
            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <a href="{{ route('teacher.exams.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors aiq-button aiq-button-secondary">
                    Cancel
                </a>
                <button
                    type="submit"
                    class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 text-white font-semibold text-xs px-6 py-2.5 rounded-xl shadow-lg shadow-indigo-500/20 transition-all duration-200 hover:scale-[1.02] active:scale-95 cursor-pointer aiq-button aiq-button-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Save Changes</span>
                </button>
            </div>

        </form>

    </div>

</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const totalItems = document.getElementById('total_items');
    const passingScore = document.getElementById('passing_score');

    if (!totalItems || !passingScore) return;

    function updatePassingScore() {
        const items = parseInt(totalItems.value, 10) || 0;
        passingScore.value = items > 0 ? Math.ceil(items * 0.75) : '';
    }

    totalItems.addEventListener('input', updatePassingScore);
    updatePassingScore();
});
</script>
@endsection