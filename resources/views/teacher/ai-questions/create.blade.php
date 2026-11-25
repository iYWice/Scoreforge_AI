@extends('layouts.app')

@section('title', 'AI Question Builder')
@section('eyebrow', 'Exam Workspace')

@section('content')

<style>
    .aiq-builder-page {
        --aiq-purple: #7047df; --aiq-ink: #292437; --aiq-pink: #f4a8e9;
        --aiq-yellow: #ffe16a; --aiq-cream: #fffdf8;
        color: var(--aiq-ink);
    }
    .builder-hero {
        position: relative; overflow: hidden; isolation: isolate;
        margin-bottom: 25px; padding: clamp(20px, 3.5vw, 30px);
        border: 1.5px solid #33253f; border-radius: 15px;
        background: linear-gradient(125deg, #ffe16a 0%, #fff1b5 58%, #f2eaff 100%);
        box-shadow: 6px 7px 0 #33253f;
    }
    .builder-hero::after {
        content: ""; position: absolute; z-index: -1; width: 105px; height: 105px;
        right: 9%; top: 25%; border-radius: 50%;
        background: radial-gradient(circle at 30% 25%, #fff 0 4%, transparent 6%), radial-gradient(circle at 35% 30%, #f5a4d8, #7047df 60%, #35bdae 100%);
        box-shadow: inset -12px -12px 17px #30243e55, 6px 8px 0 #33253f;
        transform: rotate(-14deg); animation: builder-orbit 5s ease-in-out infinite;
    }
    .builder-hero h2 { color: var(--aiq-ink) !important; letter-spacing: -.055em; }
    .builder-hero > p, .builder-hero > div > p:last-child { color: #62576b !important; }
    .builder-hero .text-indigo-600 { color: #5937bd !important; }
    .builder-form { perspective: 1200px; }
    .builder-panel {
        --tilt-x: 0deg; --tilt-y: 0deg;
        border: 1.5px solid #382d43 !important; border-radius: 13px !important;
        background: var(--aiq-cream) !important; box-shadow: 0 4px 0 #382d43;
        transform: perspective(1100px) rotateX(var(--tilt-x)) rotateY(var(--tilt-y));
        transform-style: preserve-3d; transition: transform .16s ease, box-shadow .2s;
    }
    .builder-panel:hover { box-shadow: 0 7px 0 #382d43, 0 15px 28px #35234414; }
    .builder-panel h3 { color: var(--aiq-ink) !important; }
    .builder-panel label:not(.file-drop) { color: #554b60; }
    .file-drop {
        min-height: 150px; border-color: #9676e9 !important; border-radius: 11px !important;
        background: linear-gradient(135deg, #f5efff, #fff9e2) !important;
        transition: transform .2s, border-color .2s, box-shadow .2s;
    }
    .file-drop:hover { transform: translateY(-3px); border-color: #7047df !important; box-shadow: 0 5px 0 #7047df30; }
    .question-type-option {
        border: 1px solid #3a2d4330 !important; border-radius: 9px !important;
        background: #fff !important; transition: transform .18s, background .18s, box-shadow .18s;
    }
    .question-type-option:hover, .question-type-option:focus-within { transform: translateY(-2px); background: #f9e5f6 !important; box-shadow: 0 3px 0 #382d4330; }
    .builder-page-notice { border: 1.5px solid #33253f !important; border-radius: 11px !important; background: #f4a8e9 !important; box-shadow: 3px 4px 0 #33253f; }
    .builder-page-notice p { color: #44354a !important; }
    .builder-submit {
        border: 1.5px solid #30243e !important; border-radius: 9px !important;
        background: #7047df !important; box-shadow: 0 5px 0 #30243e;
        transition: transform .18s, box-shadow .18s, background .18s;
    }
    .builder-submit:hover { background: #5c39c5 !important; transform: translateY(-2px); box-shadow: 0 7px 0 #30243e; }
    .builder-validation { border: 1.5px solid #33253f !important; border-radius: 11px !important; box-shadow: 3px 4px 0 #33253f; }
    .builder-back { display: inline-flex; padding: 7px 10px; border-radius: 8px; background: #f1eaff; color: #5937bd !important; transition: transform .18s, background .18s; }
    .builder-back:hover { transform: translateX(-3px); background: #f4a8e9; }
    @keyframes builder-orbit { 0%,100% { translate: 0 0; } 50% { translate: 0 -7px; } }
    @media (max-width: 600px) { .builder-hero::after { width: 66px; height: 66px; right: 6%; top: 16%; } }
    @media (prefers-reduced-motion: reduce) { .aiq-builder-page *, .aiq-builder-page *::before, .aiq-builder-page *::after { transition-duration: .01ms !important; animation-duration: .01ms !important; animation-iteration-count: 1 !important; } }
</style>


<div
    class="aiq-builder-page mx-auto max-w-4xl"
    x-data="{
        fileName: '',
        generating: false
    }">

    {{-- Back --}}
    <div class="mb-6">
        <a
            href="{{ route('teacher.exams.show', $exam) }}"
            class="aiq-button aiq-button-secondary builder-back text-sm font-semibold text-slate-500
                   transition hover:text-indigo-600">
            ← Back to Exam
        </a>
    </div>


    {{-- Exam --}}
    <div class="builder-hero mb-8">

        <p class="text-sm font-medium text-indigo-600">
            {{ $exam->subject?->name ?? 'No Subject' }}
        </p>

        <h2
            class="mt-1 text-2xl font-bold
                   tracking-tight text-slate-950">
            {{ $exam->title }}
        </h2>

        <p class="mt-2 text-sm text-slate-500">
            Generate draft examination questions
            from your learning material.
        </p>

    </div>


    {{-- Validation --}}
    @if ($errors->any())

    <div
        class="builder-validation mb-6 rounded-xl border border-red-200
                   bg-red-50 p-4">
        <p class="font-semibold text-red-800">
            Question generation failed.
        </p>

        <ul
            class="mt-2 list-disc space-y-1
                       pl-5 text-sm text-red-700">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>

    @endif


    <form
        method="POST"
        action="{{ route(
            'teacher.ai-questions.generate',
            $exam
        ) }}"
        enctype="multipart/form-data"
        @submit="generating = true"
        class="builder-form space-y-6">

        @csrf


        {{-- PDF --}}
        <section
            class="aiq-panel aiq-panel-interactive builder-panel rounded-2xl border border-slate-200
                   bg-white p-6" data-3d-card>

            <div class="mb-5">
                <h3
                    class="font-semibold text-slate-950">
                    Learning Material
                </h3>

                <p
                    class="mt-1 text-sm text-slate-500">
                    Upload a PDF containing the material
                    AI-Q should use to generate questions.
                </p>
            </div>


            <label
                class="aiq-dropzone file-drop flex cursor-pointer flex-col
                       items-center justify-center
                       rounded-xl border-2 border-dashed
                       border-slate-300 px-6 py-10
                       text-center transition
                       hover:border-indigo-400
                       hover:bg-indigo-50/40">

                <input
                    type="file"
                    name="source_file"
                    accept=".pdf,application/pdf"
                    required
                    class="hidden"

                    @change="
                        fileName =
                            $event.target.files[0]
                            ? $event.target.files[0].name
                            : ''
                    ">

                <div
                    class="text-sm font-semibold
                           text-slate-700"
                    x-text="
                        fileName
                            ? fileName
                            : 'Choose PDF'
                    ">
                    Choose PDF
                </div>

                <p
                    class="mt-2 text-xs text-slate-500"
                    x-show="!fileName">
                    Maximum file size: 10 MB
                </p>

                <p
                    class="mt-2 text-xs font-medium
                           text-indigo-600"
                    x-show="fileName"
                    x-cloak>
                    PDF selected
                </p>

            </label>

        </section>


        {{-- Configuration --}}
        <section
            class="aiq-panel aiq-panel-interactive builder-panel rounded-2xl border border-slate-200
                   bg-white p-6" data-3d-card>

            <h3
                class="mb-5 font-semibold
                       text-slate-950">
                Generation Settings
            </h3>


            {{-- Count --}}
            <div class="mb-6">

                <label
                    class="mb-2 block text-sm
                           font-semibold text-slate-700">
                    Number of Questions
                </label>

                <input
                    type="number"
                    name="question_count"
                    min="1"
                    max="50"
                    value="{{ old(
                        'question_count',
                        10
                    ) }}"
                    required
                    class="aiq-field w-full rounded-xl
                           border-slate-300
                           focus:border-indigo-500
                           focus:ring-indigo-500">

            </div>


            {{-- Types --}}
            <div class="mb-6">

                <label
                    class="mb-3 block text-sm
                           font-semibold text-slate-700">
                    Question Types
                </label>


                <div
                    class="grid gap-3 sm:grid-cols-3">

                    <label
                        class="aiq-option question-type-option flex cursor-pointer
                               items-center gap-3
                               rounded-xl border
                               border-slate-200 p-4">

                        <input
                            type="checkbox"
                            name="question_types[]"
                            value="multiple_choice"
                            checked
                            class="aiq-check rounded
                                   border-slate-300
                                   text-indigo-600
                                   focus:ring-indigo-500">

                        <span
                            class="text-sm font-medium
                                   text-slate-700">
                            Multiple Choice
                        </span>

                    </label>


                    <label
                        class="aiq-option question-type-option flex cursor-pointer
                               items-center gap-3
                               rounded-xl border
                               border-slate-200 p-4">

                        <input
                            type="checkbox"
                            name="question_types[]"
                            value="true_false"
                            checked
                            class="aiq-check rounded
                                   border-slate-300
                                   text-indigo-600
                                   focus:ring-indigo-500">

                        <span
                            class="text-sm font-medium
                                   text-slate-700">
                            True / False
                        </span>

                    </label>


                    <label
                        class="aiq-option question-type-option flex cursor-pointer
                               items-center gap-3
                               rounded-xl border
                               border-slate-200 p-4">

                        <input
                            type="checkbox"
                            name="question_types[]"
                            value="identification"
                            checked
                            class="aiq-check rounded
                                   border-slate-300
                                   text-indigo-600
                                   focus:ring-indigo-500">

                        <span
                            class="text-sm font-medium
                                   text-slate-700">
                            Identification
                        </span>

                    </label>

                </div>

            </div>


            {{-- Difficulty --}}
            <div>

                <label
                    class="mb-2 block text-sm
                           font-semibold text-slate-700">
                    Difficulty
                </label>

                <select
                    name="difficulty"
                    class="aiq-field w-full rounded-xl
                           border-slate-300
                           focus:border-indigo-500
                           focus:ring-indigo-500">

                    <option value="">
                        Mixed Difficulty
                    </option>

                    <option value="easy">
                        Easy
                    </option>

                    <option value="medium">
                        Medium
                    </option>

                    <option value="hard">
                        Hard
                    </option>

                </select>

            </div>

        </section>


        {{-- Notice --}}
        <div
            class="aiq-alert builder-page-notice rounded-xl border border-indigo-100
                   bg-indigo-50 px-5 py-4">
            <p
                class="text-sm leading-6
                       text-indigo-900">
                Generated questions are drafts.
                They will not be added to the exam
                until you review and approve them.
            </p>
        </div>


        {{-- Generate --}}
        <div class="flex justify-end">

            <button
                type="submit"
                :disabled="generating"
                class="aiq-button aiq-button-primary builder-submit rounded-xl bg-indigo-600
                       px-6 py-3 text-sm font-semibold
                       text-white transition
                       hover:bg-indigo-700
                       disabled:cursor-not-allowed
                       disabled:opacity-60">

                <span x-show="!generating">
                    ✦ Generate Questions
                </span>

                <span
                    x-show="generating"
                    x-cloak>
                    Generating Questions...
                </span>

            </button>

        </div>

    </form>

</div>


@push('scripts')
<script>
    (() => {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
        document.querySelectorAll('[data-3d-card]').forEach(card => {
            card.addEventListener('pointermove', event => {
                if (event.pointerType === 'touch') return;
                const box = card.getBoundingClientRect();
                card.style.setProperty('--tilt-x', `${(0.5 - (event.clientY - box.top) / box.height) * 4}deg`);
                card.style.setProperty('--tilt-y', `${((event.clientX - box.left) / box.width - 0.5) * 6}deg`);
            });
            card.addEventListener('pointerleave', () => {
                card.style.setProperty('--tilt-x', '0deg');
                card.style.setProperty('--tilt-y', '0deg');
            });
        });
    })();
</script>
@endpush

@endsection