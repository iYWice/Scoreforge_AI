@extends('layouts.app')

@section('title', 'Classes')
@section('eyebrow', 'Teacher Workspace')



@section('content')

<style>
    .aiq-class-index { --aiq-purple:#7047df; --aiq-ink:#292437; --aiq-pink:#f4a8e9; --aiq-yellow:#ffe16a; color:var(--aiq-ink); }
    .classes-hero { position:relative; overflow:hidden; isolation:isolate; padding:23px; border:1.5px solid #33253f; border-radius:15px; background:linear-gradient(120deg,#ffe16a,#fff1b5 55%,#f2eaff); box-shadow:5px 6px 0 #33253f; }
    .classes-hero h1 { color:#292437 !important; letter-spacing:-.055em; }
    .classes-hero > div:first-child, .classes-hero > button { position:relative; z-index:1; }
    .classes-hero::after { content:""; position:absolute; z-index:0; right:8%; top:17%; width:88px; height:88px; border-radius:50%; background:radial-gradient(circle at 30% 25%,#fff 0 4%,transparent 6%),radial-gradient(circle at 35% 30%,#f5a4d8,#7047df 60%,#35bdae 100%); box-shadow:inset -10px -10px 15px #30243e55,5px 7px 0 #33253f; }
    .classes-create-button,.class-index-empty button[type="button"],.class-modal button[type="submit"] { border:1px solid #30243e !important; border-radius:9px !important; background:#7047df !important; box-shadow:0 4px 0 #30243e; transition:transform .18s,box-shadow .18s,background .18s; }
    .classes-create-button:hover,.class-index-empty button[type="button"]:hover,.class-modal button[type="submit"]:hover { background:#5936c0 !important; transform:translateY(-2px); box-shadow:0 6px 0 #30243e; }
    .class-index-card { border:1.5px solid #382d43 !important; border-radius:12px !important; background:#fffdf8 !important; box-shadow:0 4px 0 #382d43; transition:transform .2s,box-shadow .2s; }
    .class-index-card:hover { transform:translateY(-5px) rotate(-.4deg); box-shadow:0 9px 0 #382d43,0 18px 28px #382d431a !important; }
    .class-index-card h2 { color:#292437 !important; }
    .class-index-card .rounded-md { border:1px solid #382d4330; border-radius:6px; background:#f4a8e9 !important; color:#382d43 !important; }
    .class-index-card .border-t { border-color:#e5dce9 !important; }
    .class-index-card .font-mono { color:#4f3a65 !important; }
    .class-index-empty { border:1.5px dashed #7047df !important; border-radius:13px !important; background:#fffdf8 !important; box-shadow:0 4px 0 #7047df40; }
    .class-index-empty .rounded-xl { border:1px solid #33253f; background:#f4a8e9 !important; color:#382d43 !important; box-shadow:2px 3px 0 #33253f; }
    .class-index-message { border:1.5px solid #33253f !important; border-radius:10px !important; box-shadow:3px 4px 0 #33253f; }
    .class-modal-backdrop { backdrop-filter:blur(5px); }
    .class-modal { border:1.5px solid #33253f; border-radius:14px; background:#fffdf8 !important; box-shadow:7px 8px 0 #33253f,0 24px 60px #30243e40 !important; }
    .class-modal h2 { color:#292437 !important; }
    .class-modal input { border:1px solid #ded5e7; border-radius:9px; padding:10px 12px; }
    .class-modal input:focus { outline:none; border-color:#9d81ed; box-shadow:0 0 0 4px #7047df1c; }
    .class-modal button[type="button"] { border-radius:8px; transition:background .16s,transform .16s; }
    .class-modal button[type="button"]:hover { background:#f4a8e9; }
    @media(max-width:640px){.classes-hero{padding:16px}.classes-hero::after{width:60px;height:60px;right:5%;top:14%}}
    @media(prefers-reduced-motion:reduce){.aiq-class-index *,.aiq-class-index *::before,.aiq-class-index *::after{transition-duration:.01ms!important;animation-duration:.01ms!important}}
</style>

<div class="aiq-class-index mx-auto max-w-7xl px-6 py-8"
    x-data="{ createOpen: false }">

    {{-- Header --}}
    <div class="classes-hero flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-600">
                Academic Workspace
            </p>

            <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950">
                Classes
            </h1>

            <p class="mt-2 max-w-2xl text-sm text-slate-500">
                Organize students, subjects, and examinations in one workspace.
            </p>
        </div>

        <button
            type="button"
            @click="createOpen = true"
            class="classes-create-button inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 aiq-button aiq-button-primary">
            + Create Class
        </button>
    </div>

    {{-- Messages --}}
    @if(session('success'))
    <div class="class-index-message mt-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 aiq-alert aiq-alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="class-index-message mt-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 aiq-alert aiq-alert-error">
        <p class="font-semibold">
            Please check the information below.
        </p>

        <ul class="mt-2 list-disc pl-5">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Classes --}}
    <div class="mt-8">
        @if($classes->isEmpty())

        <div class="class-index-empty rounded-xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center">
            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 text-xl font-semibold text-indigo-600">
                C
            </div>

            <h2 class="mt-4 text-lg font-semibold text-slate-900">
                No classes yet
            </h2>

            <p class="mx-auto mt-2 max-w-md text-sm text-slate-500">
                Create your first class to organize students and publish examinations.
            </p>

            <button
                type="button"
                @click="createOpen = true"
                class="mt-5 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 aiq-button aiq-button-primary">
                Create Class
            </button>
        </div>

        @else

        <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
            @foreach($classes as $class)

            <div
                x-data="{ editOpen: false }"
                class="class-index-card group rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-indigo-200 hover:shadow-md aiq-panel aiq-panel-interactive">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="mt-2 text-lg font-semibold text-slate-950">
                            {{ $class->name }}
                        </h2>
                    </div>

                    <span class="rounded-md bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-600 aiq-badge">
                        {{ $class->students_count }}
                        {{ Str::plural('student', $class->students_count) }}
                    </span>
                </div>

                <div class="mt-6 border-t border-slate-100 pt-4">
                    <p class="text-xs uppercase tracking-wider text-slate-400">
                        Class Code
                    </p>

                    <div class="mt-1 flex items-center justify-between">
                        <span class="font-mono text-lg font-semibold tracking-[0.15em] text-slate-800">
                            {{ $class->class_code }}
                        </span>

                        <a
                            href="{{ route('teacher.classes.show', $class) }}"
                            class="text-sm font-medium text-indigo-600 transition group-hover:translate-x-1">
                            Open →
                        </a>
                    </div>
                </div>

                <div class="mt-4 flex justify-end border-t border-slate-100 pt-3">
                    <button
                        type="button"
                        @click="editOpen = !editOpen"
                        class="text-xs font-bold text-slate-600 hover:text-indigo-600">
                        <span x-text="editOpen ? 'Cancel' : 'Edit class'"></span>
                    </button>
                </div>

                <form
                    x-show="editOpen"
                    x-cloak
                    x-transition
                    method="POST"
                    action="{{ route('teacher.classes.update', $class) }}"
                    class="mt-4 space-y-3 border-t border-slate-100 pt-4">
                    @csrf
                    @method('PATCH')

                    <label for="class-name-{{ $class->id }}" class="text-xs font-bold uppercase tracking-wider text-slate-500">
                        Class name
                    </label>
                    <input
                        id="class-name-{{ $class->id }}"
                        name="name"
                        type="text"
                        value="{{ old('name', $class->name) }}"
                        required
                        maxlength="255"
                        class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 aiq-field">

                    <div class="flex justify-end">
                        <button
                            type="submit"
                            class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                            Save class
                        </button>
                    </div>
                </form>
            </div>

            @endforeach
        </div>

        @endif
    </div>

    {{-- Create Class Modal --}}
    <div
        x-cloak
        x-show="createOpen"
        x-transition.opacity
        class="class-modal-backdrop fixed inset-0 z-50 flex items-center justify-center bg-slate-950/40 px-4"
        @keydown.escape.window="createOpen = false">
        <div
            @click.outside="createOpen = false"
            class="class-modal w-full max-w-lg rounded-xl bg-white p-6 shadow-2xl">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-slate-950">
                        Create Class
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        AI-Q will automatically generate a class code.
                    </p>
                </div>

                <button
                    type="button"
                    @click="createOpen = false"
                    class="text-xl text-slate-400 hover:text-slate-700 aiq-button aiq-button-secondary">
                    ×
                </button>
            </div>

            <form
                method="POST"
                action="{{ route('teacher.classes.store') }}"
                class="mt-6 space-y-5">
                @csrf

                <div>
                    <label
                        for="name"
                        class="text-sm font-medium text-slate-700">
                        Class Name
                    </label>

                    <input
                        id="name"
                        name="name"
                        type="text"
                        value="{{ old('name') }}"
                        placeholder="e.g. BSIT 3A"
                        required
                        class="mt-2 w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 aiq-field">
                </div>


                <div class="flex justify-end gap-3 border-t border-slate-100 pt-5">
                    <button
                        type="button"
                        @click="createOpen = false"
                        class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 aiq-button aiq-button-secondary">
                        Cancel
                    </button>

                    <button
                        type="submit"
                        class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 aiq-button aiq-button-primary">
                        Create Class
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection