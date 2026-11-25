@extends('layouts.app')

@section('title', 'Generated Questions')
@section('eyebrow', 'AI Question Builder')

@section('content')

<style>
    .aiq-review-page {
        --aiq-purple: #7047df; --aiq-ink: #292437; --aiq-pink: #f4a8e9;
        --aiq-yellow: #ffe16a; --aiq-cream: #fffdf8;
        max-width: 64rem; margin-inline: auto; color: var(--aiq-ink);
    }
    .review-hero {
        position: relative; overflow: hidden; isolation: isolate;
        margin-bottom: 24px; padding: clamp(20px, 4vw, 32px);
        border: 1.5px solid #33253f; border-radius: 15px;
        background: linear-gradient(125deg, #ffe16a 0%, #fff1b5 58%, #f2eaff 100%);
        box-shadow: 6px 7px 0 #33253f;
    }
    .review-hero::after {
        content: ""; position: absolute; z-index: -1; width: 104px; height: 104px;
        right: 8%; top: 25%; border-radius: 50%;
        background: radial-gradient(circle at 30% 25%, #fff 0 4%, transparent 6%), radial-gradient(circle at 35% 30%, #f5a4d8, #7047df 60%, #35bdae 100%);
        box-shadow: inset -12px -12px 17px #30243e55, 6px 8px 0 #33253f;
        transform: rotate(-14deg);
    }
    .review-hero h2 { color: var(--aiq-ink) !important; letter-spacing: -.055em; }
    .review-hero p { color: #62576b !important; }
    .review-alert, .review-notice {
        border: 1.5px solid #33253f !important; border-radius: 11px !important;
        box-shadow: 3px 4px 0 #33253f;
    }
    .review-alert { background: #e9f4e4 !important; color: #315b39 !important; }
    .review-notice { background: #f3eaff !important; color: #4d328d !important; }
    .review-action-bar {
        border: 1.5px solid #33253f !important; border-radius: 13px !important;
        background: #f4a8e9 !important; box-shadow: 4px 5px 0 #33253f;
    }
    .review-action-bar p { color: #35263f !important; }
    .review-action-bar button { border-radius: 8px !important; box-shadow: 0 3px 0 #33253f; transition: transform .18s, box-shadow .18s; }
    .review-action-bar button:hover { transform: translateY(-2px); box-shadow: 0 5px 0 #33253f; }
    .question-review-card {
        --tilt-x: 0deg; --tilt-y: 0deg;
        border: 1.5px solid #3a2d43 !important; border-radius: 13px !important;
        background: var(--aiq-cream) !important; box-shadow: 0 4px 0 #3a2d43;
        transform: perspective(900px) rotateX(var(--tilt-x)) rotateY(var(--tilt-y));
        transform-style: preserve-3d; transition: transform .16s ease, box-shadow .2s;
    }
    .question-review-card:hover { box-shadow: 0 8px 0 #3a2d43, 0 15px 30px #35234418; }
    .question-review-card h3 { color: var(--aiq-ink) !important; }
    .question-review-card .rounded-full { border: 1px solid #3a2d4330; background: #efe8ff !important; color: #5937bd !important; }
    .question-review-card .bg-slate-50 { border: 1px solid #e8e2ec; border-radius: 8px; background: #fff !important; }
    .question-review-card .border-t { border-color: #e8e2ec !important; }
    .review-editor {
        max-width: 64rem; margin: 18px auto 0; padding: 16px 20px;
        border: 1.5px solid #33253f; border-radius: 12px; background: #fffdf8;
        box-shadow: 4px 5px 0 #33253f;
    }
    .review-editor summary { color: #5937bd !important; }
    .review-editor form { padding-top: 8px; }
    .review-editor input:not([type="checkbox"]), .review-editor textarea {
        border: 1px solid #ddd5e5; border-radius: 8px; background: white; padding: 10px 12px;
    }
    .review-editor input:focus, .review-editor textarea:focus { outline: none; border-color: #9d81ed; box-shadow: 0 0 0 4px #7047df1c; }
    .review-editor button[type="submit"] { border: 1px solid #33253f; border-radius: 8px; background: #7047df; box-shadow: 0 4px 0 #33253f; }
    .review-editor button[type="submit"]:hover { background: #5936c0; }
    @media (max-width: 600px) { .review-hero::after { width: 64px; height: 64px; right: 6%; top: 13%; opacity: .85; } }
    @media (prefers-reduced-motion: reduce) { .aiq-review-page *, .aiq-review-page *::before, .aiq-review-page *::after { transition-duration: .01ms !important; animation-duration: .01ms !important; } }
</style>

<div class="aiq-review-page mx-auto max-w-5xl">
    <div class="review-hero mb-8">
        <a
            href="{{ route('teacher.ai-questions.create', $exam) }}"
            class="text-sm font-semibold text-slate-500 hover:text-indigo-600">
            ← Back to Question Builder
        </a>

        <h2 class="mt-5 text-2xl font-bold text-slate-950">
            Generated Questions
        </h2>

        <p class="mt-2 text-sm text-slate-500">
            {{ count($session->generated_questions ?? []) }}
            questions from {{ $session->source_name }}.
        </p>

        @if(session('success'))
        <div class="aiq-alert aiq-alert-success review-alert mt-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
            {{ session('success') }}
        </div>
        @endif
    </div>

    @if($session->status === 'used')
    <div class="aiq-alert aiq-alert-info review-notice mb-5 rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-3 text-sm text-indigo-800">
        These questions have already been added to an examination.
    </div>
    @else
    <form
        method="POST"
        action="{{ route('teacher.ai-questions.commit', [$exam, $session]) }}">
        @csrf

        <div class="aiq-action-bar review-action-bar mb-5 flex flex-col gap-3 rounded-2xl border border-indigo-100 bg-indigo-50/70 p-4 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-sm text-indigo-950">
                Select questions to save or add to <strong>{{ $exam->title }}</strong>.
            </p>

            <div class="flex flex-wrap gap-2">
                @if($session->purpose !== 'question_bank')
                <button
                    type="submit"
                    name="action"
                    value="save"
                    class="aiq-button aiq-button-secondary rounded-xl border border-indigo-200 bg-white px-4 py-2.5 text-sm font-semibold text-indigo-700 hover:bg-indigo-100">
                    Save selected for later
                </button>
                @endif

                <button
                    type="submit"
                    name="action"
                    value="use"
                    class="aiq-button aiq-button-primary rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">
                    Add selected to this exam
                </button>
            </div>
        </div>

        <div class="space-y-4">
            @foreach($session->generated_questions ?? [] as $index => $question)
            <article class="aiq-panel aiq-panel-interactive question-review-card rounded-2xl border border-slate-200 bg-white p-6" data-3d-card>
                <div class="mb-4 flex items-start justify-between gap-4">
                    <div class="flex items-start gap-3">
                        <input
                            type="checkbox"
                            name="selected_questions[]"
                            value="{{ $index }}"
                            checked
                            class="aiq-check mt-1 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">

                        <h3 class="font-semibold text-slate-950">
                            {{ $index + 1 }}. {{ $question['question_text'] }}
                        </h3>
                    </div>

                    <span class="aiq-badge shrink-0 rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">
                        {{ str_replace('_', ' ', $question['question_type']) }}
                    </span>
                </div>

                <p class="mb-4 text-xs font-semibold uppercase tracking-wide text-slate-400">
                    {{ $question['topic'] ?? 'General' }}
                </p>

                @if(!empty($question['options']))
                <div class="mb-4 space-y-2">
                    @foreach($question['options'] as $option)
                    <div class="aiq-option rounded-lg bg-slate-50 px-4 py-2 text-sm text-slate-700">
                        {{ $option }}
                    </div>
                    @endforeach
                </div>
                @endif

                <div class="border-t border-slate-100 pt-4 text-sm">
                    <span class="font-semibold text-slate-500">Correct Answer:</span>
                    <span class="ml-1 font-semibold text-emerald-700">
                        {{ $question['correct_answer'] }}
                    </span>
                </div>
            </article>
            @endforeach
        </div>
    </form>
    @endif
</div>
<details class="aiq-panel review-editor mt-5 border-t border-slate-100 pt-4">
    <summary class="cursor-pointer text-sm font-semibold text-indigo-600">
        Edit question
    </summary>

    <form
        method="POST"
        action="{{ route('teacher.ai-questions.update-question', [
            'exam' => $exam->id,
            'session' => $session->id,
            'questionIndex' => $index,
        ]) }}"
        class="mt-4 space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">
                Question
            </label>
            <textarea
                name="question_text"
                required
                maxlength="1000"
                class="aiq-field w-full rounded-lg border-slate-300">{{ old('question_text', $question['question_text']) }}</textarea>
        </div>

        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">
                Topic
            </label>
            <input
                name="topic"
                value="{{ old('topic', $question['topic'] ?? '') }}"
                maxlength="255"
                class="aiq-field w-full rounded-lg border-slate-300">
        </div>

        @if($question['question_type'] === 'multiple_choice')
        @foreach($question['options'] as $optionIndex => $option)
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">
                Choice {{ $optionIndex + 1 }}
            </label>
            <input
                name="options[]"
                value="{{ $option }}"
                required
                maxlength="500"
                class="w-full rounded-lg border-slate-300">
        </div>
        @endforeach
        @endif

        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">
                Correct answer
            </label>
            <input
                name="correct_answer"
                value="{{ old('correct_answer', $question['correct_answer']) }}"
                required
                maxlength="500"
                class="w-full rounded-lg border-slate-300">
            @if($question['question_type'] === 'multiple_choice')
            <p class="mt-1 text-xs text-slate-500">
                For multiple choice, this must exactly match one of the choices.
            </p>
            @endif
        </div>

        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">
                Points
            </label>
            <input
                type="number"
                name="points"
                value="{{ old('points', $question['points'] ?? 1) }}"
                min="1"
                max="100"
                required
                class="aiq-field w-32 rounded-lg border-slate-300">
        </div>

        <button
            type="submit"
            class="aiq-button aiq-button-primary rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
            Save changes
        </button>
    </form>
</details>

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