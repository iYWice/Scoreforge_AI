@extends('layouts.app')

@section('title', $class->name)
@section('eyebrow', 'Class Workspace')

@section('content')

<style>
    .aiq-class-show { --aiq-purple:#7047df; --aiq-ink:#292437; --aiq-pink:#f4a8e9; --aiq-yellow:#ffe16a; color:var(--aiq-ink); }
    .class-show-hero { position:relative; overflow:hidden; isolation:isolate; padding:24px; border:1.5px solid #33253f; border-radius:15px; background:linear-gradient(120deg,#ffe16a,#fff1b5 55%,#f2eaff); box-shadow:5px 6px 0 #33253f; }
    .class-show-hero::after { content:""; position:absolute; z-index:-1; right:8%; top:18%; width:86px; height:86px; border-radius:50%; background:radial-gradient(circle at 30% 25%,#fff 0 4%,transparent 6%),radial-gradient(circle at 35% 30%,#f5a4d8,#7047df 60%,#35bdae 100%); box-shadow:inset -10px -10px 15px #30243e55,5px 7px 0 #33253f; }
    .class-show-hero h1 { color:#292437 !important; letter-spacing:-.055em; }
    .class-show-hero > div:last-child { border:1.5px solid #33253f !important; border-radius:11px !important; background:#f4a8e9 !important; box-shadow:3px 4px 0 #33253f; }
    .class-show-hero > div:last-child p { color:#49364f !important; }
    .class-show-hero #class-code { color:#33253f !important; }
    .class-show-hero button { padding:6px 9px; border-radius:7px; background:#fffdf8; color:#5937bd !important; }
    .class-show-hero button:hover { background:#ffe16a; }
    .class-stats > div { border:1.5px solid #382d43 !important; border-radius:11px !important; background:#fffdf8 !important; box-shadow:0 4px 0 #382d43; transition:transform .2s,box-shadow .2s; }
    .class-stats > div:nth-child(2) { background:#f9e4f6 !important; }
    .class-stats > div:hover { transform:translateY(-4px) rotate(-.4deg); box-shadow:0 8px 0 #382d43; }
    .class-list-card { overflow:hidden; border:1.5px solid #382d43 !important; border-radius:12px !important; background:#fffdf8 !important; box-shadow:0 4px 0 #382d43; }
    .class-show-student { transform-style:preserve-3d; transition:transform .2s,background .2s; }
    .class-show-student:hover { transform:translateY(-2px) rotateX(1deg); background:#fff9e4; }
    .class-show-student .rounded-full { border:1px solid #382d4330; background:#f4a8e9 !important; color:#382d43 !important; }
    .class-show-exam { transition:transform .18s,background .18s; }
    .class-show-exam:hover { transform:translateX(4px); background:#f3eaff !important; }
    .class-show-create { border:1px solid #30243e; border-radius:8px; background:#7047df !important; box-shadow:0 4px 0 #30243e; }
    .class-show-create:hover { background:#5936c0 !important; transform:translateY(-2px); box-shadow:0 6px 0 #30243e; }
    .class-show-back { display:inline-flex; padding:7px 10px; border-radius:8px; background:#f1eaff; color:#5937bd !important; }
    .class-show-back:hover { background:#f4a8e9; }
    @media(max-width:640px){.class-show-hero{padding:16px}.class-show-hero::after{width:58px;height:58px;right:5%;top:13%}}
    @media(prefers-reduced-motion:reduce){.aiq-class-show *,.aiq-class-show *::before,.aiq-class-show *::after{transition-duration:.01ms!important;animation-duration:.01ms!important}}
</style>

<div class="aiq-class-show mx-auto max-w-7xl space-y-8 px-4 py-8 sm:px-6 lg:px-8">

    {{-- Back --}}
    <a
        href="{{ route('teacher.classes.index') }}"
        class="class-show-back text-sm font-medium text-slate-500 hover:text-indigo-600 aiq-button aiq-button-secondary">
        ← Classes
    </a>

    {{-- Class header --}}
    <header class="class-show-hero flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-600">
                {{ $class->subject?->name ?? 'No Subject' }}
            </p>

            <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950">
                {{ $class->name }}
            </h1>

            <p class="mt-2 text-sm text-slate-500">
                Manage students and examinations assigned to this class.
            </p>
        </div>

        <div class="w-full rounded-xl border border-indigo-100 bg-indigo-50 px-5 py-4 sm:w-auto">
            <p class="text-xs font-semibold uppercase tracking-wider text-indigo-500">
                Class Code
            </p>

            <div class="mt-1 flex items-center justify-between gap-4 sm:justify-start">
                <span
                    id="class-code"
                    class="font-mono text-xl font-bold tracking-[0.18em] text-indigo-950">
                    {{ $class->class_code }}
                </span>

                <button
                    type="button"
                    onclick="navigator.clipboard.writeText(@js($class->class_code))"
                    class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 aiq-button aiq-button-secondary">
                    Copy
                </button>
            </div>
        </div>
    </header>

    {{-- Stats --}}
    <section class="class-stats grid gap-4 sm:grid-cols-2">
        <div class="rounded-xl border border-slate-200 bg-white p-5 aiq-panel aiq-panel-interactive">
            <p class="text-sm text-slate-500">Students</p>
            <p class="mt-2 text-3xl font-semibold text-slate-950">
                {{ $class->students->count() }}
            </p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-5 aiq-panel aiq-panel-interactive">
            <p class="text-sm text-slate-500">Examinations</p>
            <p class="mt-2 text-3xl font-semibold text-slate-950">
                {{ $class->exams->count() }}
            </p>
        </div>
    </section>

    <div class="grid gap-8 lg:grid-cols-[1fr_1.2fr]">

        {{-- Students --}}
        <section>
            <div>
                <h2 class="text-lg font-semibold text-slate-950">Students</h2>
                <p class="mt-1 text-sm text-slate-500">
                    Students who joined using the class code.
                </p>
            </div>

            <div class="class-list-card mt-4 overflow-hidden rounded-xl border border-slate-200 bg-white aiq-panel">
                @forelse($class->students as $student)
                    <article class="class-show-student flex flex-col gap-4 border-b border-slate-100 px-5 py-4 last:border-0 sm:flex-row sm:items-center sm:justify-between aiq-panel-interactive">
                        <div class="flex min-w-0 items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-100 text-sm font-semibold text-slate-600">
                                {{ strtoupper(substr($student->fname ?? '', 0, 1)) }}
                            </div>

                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-slate-900">
                                    {{ $student->fname }} {{ $student->lname }}
                                </p>
                                <p class="truncate text-xs text-slate-500">
                                    {{ $student->email }}
                                </p>
                                <p class="truncate text-xs font-semibold text-slate-600">
                                    Student ID: {{ $student->student_id ?: 'Not provided' }}
                                </p>
                            </div>
                            
                        </div>

                        <div class="flex flex-wrap items-center gap-4 pl-[3.25rem] sm:pl-0">
                            <a
                                href="{{ route('teacher.students.performance', $student) }}"
                                class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 aiq-button aiq-button-secondary">
                                View performance →
                            </a>

                            <form
                                method="POST"
                                action="{{ route('teacher.classes.students.remove', [$class, $student]) }}"
                                onsubmit="return confirm('Remove this student from the class and its subject rosters? Their exam history will be kept.');">
                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="text-xs font-semibold text-rose-600 hover:text-rose-800 aiq-button aiq-button-danger">
                                    Remove from class
                                </button>
                            </form>
                        </div>
                    </article>
                @empty
                    <div class="px-6 py-12 text-center">
                        <p class="text-sm font-medium text-slate-700">No students yet</p>
                        <p class="mt-1 text-sm text-slate-500">
                            Share the class code
                            <strong class="font-mono">{{ $class->class_code }}</strong>
                            with your students.
                        </p>
                    </div>
                @endforelse
            </div>
        </section>

        {{-- Examinations --}}
        <section>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-950">
                        Class Examinations
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Examinations assigned to this class.
                    </p>
                </div>

                <a
                    href="{{ route('teacher.exams.index') }}"
                    class="inline-flex w-fit items-center rounded-lg bg-indigo-600 px-3 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700 aiq-button aiq-button-primary">
                    + Create Exam
                </a>
            </div>

            <div class="class-list-card mt-4 overflow-hidden rounded-xl border border-slate-200 bg-white aiq-panel">
                @forelse($class->exams as $exam)
                    <a
                        href="{{ route('teacher.exams.show', $exam) }}"
                        class="class-show-exam flex items-center justify-between gap-4 border-b border-slate-100 px-5 py-4 transition last:border-0 hover:bg-slate-50 aiq-panel-interactive">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-slate-900">
                                {{ $exam->title }}
                            </p>
                            <p class="mt-1 font-mono text-xs text-slate-400">
                                {{ $exam->exam_code }}
                            </p>
                        </div>

                        <span class="shrink-0 rounded-md bg-slate-100 px-2.5 py-1 text-xs font-semibold capitalize text-slate-600 aiq-badge">
                            {{ $exam->status }}
                        </span>
                    </a>
                @empty
                    <div class="px-6 py-12 text-center">
                        <p class="text-sm font-medium text-slate-700">
                            No examinations assigned
                        </p>
                        <p class="mt-1 text-sm text-slate-500">
                            Create an examination and assign it to this class.
                        </p>
                    </div>
                @endforelse
            </div>
        </section>

    </div>
</div>
@endsection