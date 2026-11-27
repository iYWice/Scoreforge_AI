{-- Use the application layout and configure inherited page structure. --}
@extends('layouts.app')

{-- Define this page section and its presentation content. --}
@section('title', 'Classes & Subjects')

{-- Define this page section and its presentation content. --}
@section('content')

<style>
    .aiq-class-subjects { --aiq-purple:#7047df; --aiq-ink:#292437; --aiq-pink:#f4a8e9; --aiq-yellow:#ffe16a; color:var(--aiq-ink); }
    .subjects-hero { position:relative; overflow:hidden; isolation:isolate; padding:22px; border:1.5px solid #33253f; border-radius:15px; background:linear-gradient(120deg,#ffe16a,#fff1b5 56%,#f2eaff); box-shadow:5px 6px 0 #33253f; }
    .subjects-hero h1 { color:#292437 !important; letter-spacing:-.05em; }
    .subjects-hero p { color:#62576b !important; }
    .subjects-hero > span { border:1px solid #33253f !important; border-radius:10px !important; background:#f4a8e9 !important; color:#382d43 !important; box-shadow:2px 3px 0 #33253f; }
    .subject-create-card { position:relative; overflow:hidden; border:1.5px solid #33253f !important; border-radius:14px !important; background:#fffdf8 !important; box-shadow:5px 6px 0 #33253f !important; transition:transform .2s,box-shadow .2s; }
    .subject-create-card:hover { transform:translateY(-4px) rotateX(1deg); box-shadow:7px 9px 0 #33253f !important; }
    .subject-create-card h2 { color:#292437 !important; }
    .subject-create-card h2 svg { color:#7047df !important; }
    .subject-create-card > div:first-child { width:5px !important; background:#7047df !important; }
    .subject-create-card:nth-child(2) > div:first-child { background:#f4a8e9 !important; }
    .subject-create-card label { color:#5e5369 !important; }
    .subject-create-card input { border:1px solid #ded5e7 !important; border-radius:9px !important; background:#fff !important; color:#292437 !important; }
    .subject-create-card input::placeholder { color:#a49aaa !important; }
    .subject-create-card input:focus { border-color:#9d81ed !important; box-shadow:0 0 0 4px #7047df1c !important; }
    .subject-create-card button[type="submit"] { border:1px solid #30243e; border-radius:9px !important; background:#7047df !important; box-shadow:0 4px 0 #30243e; }
    .subject-create-card button[type="submit"]:hover { background:#5936c0 !important; transform:translateY(-2px); box-shadow:0 6px 0 #30243e; }
    .subject-create-card button[type="submit"]:disabled { opacity:.65; }
    .managed-list-card { overflow:hidden; border:1.5px solid #382d43 !important; border-radius:13px !important; background:#fffdf8 !important; box-shadow:0 4px 0 #382d43 !important; }
    .managed-list-card > div:first-child { border-color:#e7dfeb !important; background:#f7f1ff; }
    .managed-list-card h2 { color:#292437 !important; }
    .managed-list-card .rounded-md { border:1px solid #382d4330; border-radius:6px; background:#f4a8e9 !important; color:#382d43 !important; }
    .managed-row { transition:transform .17s,background .17s; }
    .managed-row:hover { transform:translateX(3px); background:#fff7df !important; }
    .managed-row h3 { color:#292437 !important; }
    .managed-row .text-slate-400 { color:#847a8d !important; }
    .managed-row button { border-radius:8px; transition:background .16s,transform .16s; }
    .managed-row button:hover { background:#fce3f5 !important; transform:translateY(-1px); }
    .subjects-alert { border:1.5px solid #33253f !important; border-radius:10px !important; box-shadow:3px 4px 0 #33253f; }
    .subjects-success { background:#e7f3e5 !important; color:#315b39 !important; }
    .subjects-error { background:#fce6f3 !important; color:#8f315f !important; }
    @media(max-width:640px){.subjects-hero{padding:16px}.subject-create-card{padding:19px}}
    @media(prefers-reduced-motion:reduce){.aiq-class-subjects *,.aiq-class-subjects *::before,.aiq-class-subjects *::after{transition-duration:.01ms!important;animation-duration:.01ms!important}}
</style>


<div class="aiq-class-subjects max-w-6xl mx-auto space-y-8">

    {{-- Alert Messages --}}
    @if(session('success'))
    <div class="subjects-alert subjects-success flex items-center gap-3 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm font-medium aiq-alert aiq-alert-success">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>{{ session('success') }}</span>
    </div>
    @endif
    {-- Render this content conditionally based on the current view state. --}
    @if(session('error'))
    <div class="subjects-alert subjects-error flex items-center gap-3 bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 px-4 py-3 rounded-xl text-sm font-medium aiq-alert aiq-alert-error">

        <svg
            class="w-5 h-5 shrink-0"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24">

            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>

        <span>{{ session('error') }}</span>

    </div>
    @endif

    {{-- Page Header --}}
    <div class="subjects-hero flex flex-col gap-1">
        <h1 class="text-2xl font-bold tracking-tight text-white flex items-center gap-3">
            <span class="p-2 rounded-xl bg-indigo-600/20 text-indigo-400 border border-indigo-500/20">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </span>
            Classes & Subjects
        </h1>
        <p class="text-xs text-slate-400">
            Create and organize academic classes and subjects for your portal.
        </p>
    </div>


    {{-- Forms Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- CREATE CLASS CARD --}}
        <div class="subject-create-card bg-[#111827] border border-slate-800 rounded-2xl p-6 shadow-xl relative overflow-hidden group aiq-panel aiq-panel-interactive">
            <div class="absolute top-0 left-0 w-1 h-full bg-indigo-500"></div>

            <h2 class="text-base font-bold text-white mb-5 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                Create Class
            </h2>

            {-- Collect and submit the data for this operation. --}
            <form method="POST" action="{{ route('teacher.classes.store') }}" class="space-y-4" x-data="{ loading: false }" @submit="loading = true">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Class Name</label>
                    <input type="text" name="name" placeholder="Example: BSIT 3A" value="{{ old('name') }}" required
                        class="w-full px-3.5 py-2.5 text-sm bg-slate-900/80 border border-slate-800 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all aiq-field">
                    @error('name', 'classCreation')
                    <p class="text-rose-400 text-xs mt-1.5">
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                <button type="submit" :disabled="loading"
                    class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-500 active:scale-[0.98] text-white font-semibold text-xs rounded-xl shadow-lg shadow-indigo-600/20 transition-all flex items-center justify-center gap-2 disabled:opacity-50 aiq-button aiq-button-primary">
                    <svg x-show="loading" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" x-cloak>
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-text="loading ? 'Creating...' : 'Create Class'"></span>
                </button>
            </form>
        </div>

        {{-- CREATE SUBJECT CARD --}}
        <div class="subject-create-card bg-[#111827] border border-slate-800 rounded-2xl p-6 shadow-xl relative overflow-hidden group aiq-panel aiq-panel-interactive">
            <div class="absolute top-0 left-0 w-1 h-full bg-emerald-500"></div>

            <h2 class="text-base font-bold text-white mb-5 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                Create Subject
            </h2>

            {-- Collect and submit the data for this operation. --}
            <form method="POST" action="{{ route('teacher.subjects.store') }}" class="space-y-4" x-data="{ loading: false }" @submit="loading = true">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Subject Name</label>
                    <input type="text" name="name" placeholder="Example: Database Management" required
                        class="w-full px-3.5 py-2.5 text-sm bg-slate-900/80 border border-slate-800 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all aiq-field">
                    @error('name', 'subjectCreation')
                    <p class="text-rose-400 text-xs mt-1.5">
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                <button type="submit" :disabled="loading"
                    class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-500 active:scale-[0.98] text-white font-semibold text-xs rounded-xl shadow-lg shadow-emerald-600/20 transition-all flex items-center justify-center gap-2 disabled:opacity-50 aiq-button aiq-button-primary">
                    <svg x-show="loading" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" x-cloak>
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-text="loading ? 'Creating...' : 'Create Subject'"></span>
                </button>
            </form>
        </div>

    </div>

    {{-- EXISTING CLASSES CARD --}}
    <div class="managed-list-card bg-[#111827] border border-slate-800 rounded-2xl shadow-xl overflow-hidden aiq-panel">
        <div class="p-5 border-b border-slate-800/80 flex items-center justify-between">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                My Classes
            </h2>
            <span class="text-xs px-2.5 py-1 rounded-md bg-slate-800 text-slate-400 font-semibold aiq-badge">
                {{ count($classes) }} Total
            </span>
        </div>

        <div class="divide-y divide-slate-800/60">
            {-- Iterate over the related records and render each result. --}
            @forelse($classes as $class)
            <div class="managed-row p-4 sm:p-5 flex justify-between items-center hover:bg-slate-800/30 transition-colors aiq-panel-interactive">
                <div class="space-y-1">
                    <h3 class="font-semibold text-white text-sm">
                        {{ $class->name }}
                    </h3>
                    <!-- <p class="text-xs text-slate-400 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        School Year: <span class="text-slate-300 font-medium">{{ $class->school_year }}</span>
                    </p> -->
                </div>

                {-- Collect and submit the data for this operation. --}
                <form method="POST" action="{{ route('teacher.classes.destroy', $class->id) }}" onsubmit="return confirm('Delete this class?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="p-2 text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 rounded-xl transition-all aiq-button aiq-button-danger">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </form>
            </div>
            @empty
            <div class="p-8 text-center text-xs text-slate-500">
                No classes created yet.
            </div>
            @endforelse
        </div>
    </div>

    {{-- EXISTING SUBJECTS CARD --}}
    <div class="managed-list-card bg-[#111827] border border-slate-800 rounded-2xl shadow-xl overflow-hidden aiq-panel">
        <div class="p-5 border-b border-slate-800/80 flex items-center justify-between">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                My Subjects
            </h2>
            <span class="text-xs px-2.5 py-1 rounded-md bg-slate-800 text-slate-400 font-semibold aiq-badge">
                {{ count($subjects) }} Total
            </span>
        </div>

        <div class="divide-y divide-slate-800/60">
            {-- Iterate over the related records and render each result. --}
            @forelse($subjects as $subject)
            <div class="managed-row p-4 sm:p-5 flex justify-between items-center hover:bg-slate-800/30 transition-colors aiq-panel-interactive">
                <div class="space-y-1">
                    <h3 class="font-semibold text-white text-sm">
                        {{ $subject->name }}
                    </h3>
                </div>

                {-- Collect and submit the data for this operation. --}
                <form method="POST" action="{{ route('teacher.subjects.destroy', $subject->id) }}" onsubmit="return confirm('Delete this subject?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="p-2 text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 rounded-xl transition-all aiq-button aiq-button-danger">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </form>
            </div>
            @empty
            <div class="p-8 text-center text-xs text-slate-500">
                No subjects created yet.
            </div>
            @endforelse
        </div>
    </div>

</div>

@endsection