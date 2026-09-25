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

                        <span
                            class="rounded-full bg-emerald-50
                                       px-2 py-1
                                       text-[9px] font-extrabold
                                       uppercase tracking-wider
                                       text-emerald-600">
                            Published
                        </span>

                    </div>

                    <p class="mt-2 text-xs text-slate-400">
                        Exam Code:
                        <span class="font-mono font-bold text-slate-600">
                            {{ $exam->exam_code }}
                        </span>
                    </p>

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