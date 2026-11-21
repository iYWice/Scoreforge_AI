@extends('layouts.app')

@section('title', $class->name)
@section('eyebrow', 'Class Workspace')

@section('content')

<div class="space-y-8">

    <a
        href="{{ route('student.classes.index') }}"
        class="inline-flex items-center gap-2
               text-xs font-bold text-slate-400
               transition hover:text-indigo-600">
        ← My Classes
    </a>


    {{-- Header --}}
    <section
        class="flex flex-col gap-6
               border-b border-slate-200 pb-7
               lg:flex-row lg:items-end lg:justify-between">

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

            <p class="mt-1 font-mono text-lg font-black
                      tracking-[0.18em] text-slate-800">
                {{ $class->class_code }}
            </p>
        </div>

    </section>

<section>
    <h3 class="text-xl font-extrabold text-slate-950">My Subjects</h3>
    <p class="mt-1 text-sm text-slate-500">
        Subjects your teacher assigned to you in this class.
    </p>

    <div class="mt-5 grid gap-4 md:grid-cols-2">
        @forelse($class->classSubjects as $offering)
            <article class="rounded-xl border border-slate-200 bg-white p-5">
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
            <p class="text-sm text-slate-500">
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


        <div class="mt-6 border-t border-slate-200">

            @forelse($class->exams as $exam)

            <div
                class="flex flex-col gap-4
                           border-b border-slate-200 py-5
                           sm:flex-row sm:items-center
                           sm:justify-between">

                <div>

                    <div class="flex items-center gap-3">

                        <h4 class="text-sm font-bold text-slate-900">
                            {{ $exam->title }}
                        </h4>

                        @if($exam->opens_at && now()->lt($exam->opens_at))
                            <span
                                class="rounded-full bg-amber-50
                                           px-2 py-1
                                           text-[9px] font-extrabold
                                           uppercase tracking-wider
                                           text-amber-700">
                                Upcoming
                            </span>
                        @else
                            <span
                                class="rounded-full bg-emerald-50
                                           px-2 py-1
                                           text-[9px] font-extrabold
                                           uppercase tracking-wider
                                           text-emerald-600">
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
                        @if($exam->opens_at)
                            <span>Opens: {{ $exam->opens_at->format('M j, Y · g:i A') }}</span>
                        @else
                            <span>Opens: Immediately</span>
                        @endif

                        @if($exam->closes_at)
                            <span>Deadline: {{ $exam->closes_at->format('M j, Y · g:i A') }}</span>
                        @else
                            <span>No deadline</span>
                        @endif
                    </div>
                </div>


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
                                   active:scale-95">
                        Open Exam →
                    </button>
                </form>

            </div>

            @empty

            <div class="py-14 text-center">

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