@extends('layouts.app')

@section('title', 'My Classes')
@section('eyebrow', 'Student Workspace')

@section('content')

<div
    class="space-y-8"
    x-data="{ joinOpen: {{ $errors->has('class_code') ? 'true' : 'false' }} }">

    {{-- Introduction --}}
    <section
        class="flex flex-col gap-5
               sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-[10px] font-extrabold uppercase
                      tracking-[0.2em] text-indigo-600">
                Academic Classes
            </p>

            <h2 class="mt-2 text-2xl font-extrabold
                       tracking-[-0.04em] text-slate-950">
                Your enrolled classes
            </h2>

            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
                Join a class using the code provided by your teacher
                and access published examinations.
            </p>
        </div>

        <button
            type="button"
            @click="joinOpen = true"
            class="inline-flex items-center justify-center
                   rounded-xl bg-slate-950 px-4 py-2.5
                   text-xs font-bold text-white
                   transition-all duration-200
                   hover:-translate-y-0.5
                   hover:bg-indigo-600
                   active:scale-95">
            + Join Class
        </button>
    </section>


    {{-- Info message --}}
    @if(session('info'))
    <div class="border border-blue-200
                    bg-blue-50 px-4 py-3
                    text-sm font-semibold text-blue-700">
        {{ session('info') }}
    </div>
    @endif


    {{-- Classes --}}
    <section>

        @if($classes->isEmpty())

        <div
            class="border border-dashed border-slate-300
                       bg-white px-6 py-16 text-center">
            <div
                class="mx-auto flex h-12 w-12
                           items-center justify-center
                           rounded-xl bg-indigo-50
                           font-black text-indigo-600">
                C
            </div>

            <h3 class="mt-4 text-lg font-extrabold text-slate-950">
                No classes joined
            </h3>

            <p class="mx-auto mt-2 max-w-md
                          text-sm leading-6 text-slate-500">
                Enter the class code provided by your teacher
                to join your first class.
            </p>

            <button
                type="button"
                @click="joinOpen = true"
                class="mt-5 rounded-xl bg-indigo-600
                           px-4 py-2.5 text-xs font-bold
                           text-white transition
                           hover:bg-indigo-700">
                Join a Class
            </button>
        </div>

        @else

        <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">

            @foreach($classes as $class)

            <a
                href="{{ route('student.classes.show', $class) }}"
                class="group border border-slate-200
                               bg-white p-6
                               transition-all duration-300
                               hover:-translate-y-1
                               hover:border-indigo-200
                               hover:shadow-xl
                               hover:shadow-slate-200/60">

                <div class="flex items-start justify-between gap-4">

                    <div>
                        <p class="text-[10px] font-extrabold
                                          uppercase tracking-[0.18em]
                                          text-indigo-600">
                            {{ $class->subject?->name ?? 'Subject' }}
                        </p>

                        <h3 class="mt-2 text-lg font-extrabold
                                           tracking-tight text-slate-950">
                            {{ $class->name }}
                        </h3>
                    </div>

                    <span
                        class="flex h-9 w-9 items-center
                                       justify-center rounded-full
                                       bg-slate-100 text-slate-400
                                       transition-all duration-300
                                       group-hover:bg-indigo-600
                                       group-hover:text-white">
                        →
                    </span>

                </div>


                <div class="mt-6 space-y-3">

                    <div>
                        <p class="text-[10px] font-bold
                                          uppercase tracking-wider
                                          text-slate-400">
                            Teacher
                        </p>

                        <p class="mt-1 text-sm font-semibold text-slate-700">
                            {{ $class->teacher?->fname }}
                            {{ $class->teacher?->lname }}
                        </p>
                    </div>

                    <div
                        class="flex items-center justify-between
                                       border-t border-slate-100 pt-4">
                        <span class="text-xs font-semibold text-slate-400">
                            Available Exams
                        </span>

                        <span class="text-sm font-extrabold text-slate-900">
                            {{ $class->published_exams_count }}
                        </span>
                    </div>

                </div>

            </a>

            @endforeach

        </div>

        @endif

    </section>


    {{-- Join Class Modal --}}
    <div
        x-cloak
        x-show="joinOpen"
        x-transition.opacity
        @keydown.escape.window="joinOpen = false"
        class="fixed inset-0 z-[70]
               flex items-center justify-center
               bg-slate-950/50 px-4">

        <div
            @click.outside="joinOpen = false"
            class="w-full max-w-md
                   rounded-2xl bg-white p-6
                   shadow-2xl">

            <div class="flex items-start justify-between">

                <div>
                    <p class="text-[10px] font-extrabold
                              uppercase tracking-[0.2em]
                              text-indigo-600">
                        Enrollment
                    </p>

                    <h2 class="mt-2 text-xl font-extrabold text-slate-950">
                        Join a Class
                    </h2>

                    <p class="mt-2 text-sm leading-6 text-slate-500">
                        Enter the class code provided by your teacher.
                    </p>
                </div>

                <button
                    type="button"
                    @click="joinOpen = false"
                    class="text-xl text-slate-400
                           transition hover:text-slate-700">
                    ×
                </button>

            </div>


            <form
                method="POST"
                action="{{ route('student.classes.join') }}"
                class="mt-6">
                @csrf

                <label
                    for="class_code"
                    class="text-xs font-bold uppercase
                           tracking-wider text-slate-500">
                    Class Code
                </label>

                <input
                    id="class_code"
                    name="class_code"
                    type="text"
                    maxlength="10"
                    value="{{ old('class_code') }}"
                    placeholder="e.g. 0FS0TV"
                    required
                    autocomplete="off"
                    class="mt-2 w-full rounded-xl
                           border-slate-300
                           px-4 py-3
                           font-mono text-lg font-bold
                           uppercase tracking-[0.18em]
                           focus:border-indigo-500
                           focus:ring-indigo-500">

                @error('class_code')
                <p class="mt-2 text-xs font-semibold text-red-600">
                    {{ $message }}
                </p>
                @enderror


                <div
                    class="mt-6 flex justify-end gap-3
                           border-t border-slate-100 pt-5">
                    <button
                        type="button"
                        @click="joinOpen = false"
                        class="rounded-xl border border-slate-300
                               px-4 py-2.5
                               text-xs font-bold text-slate-600
                               transition hover:bg-slate-50">
                        Cancel
                    </button>

                    <button
                        type="submit"
                        class="rounded-xl bg-indigo-600
                               px-4 py-2.5
                               text-xs font-bold text-white
                               transition hover:bg-indigo-700">
                        Join Class
                    </button>
                </div>

            </form>

        </div>

    </div>

</div>

@endsection