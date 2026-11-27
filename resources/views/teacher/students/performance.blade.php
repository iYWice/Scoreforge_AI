{-- Use the application layout and configure inherited page structure. --}
@extends('layouts.app')

{-- Define this page section and its presentation content. --}
@section('title', 'Student Performance')
{-- Define this page section and its presentation content. --}
@section('eyebrow', 'Student Review')

{-- Define this page section and its presentation content. --}
@section('content')
<div class="mx-auto max-w-6xl space-y-8 px-4 py-8 sm:px-6 lg:px-8 aiq-student-performance">

    {{-- Student header --}}
    <header class="rounded-2xl border border-slate-200 bg-white p-6 aiq-panel aiq-panel-interactive">
        <a
            href="{{ route('teacher.analytics.index') }}"
            class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 aiq-button aiq-button-secondary">
            ← Back to analytics
        </a>

        <div class="mt-5 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-600">
                    Student Performance
                </p>

                <h1 class="mt-2 text-2xl font-extrabold text-slate-950 sm:text-3xl">
                    {{ $student->fname }} {{ $student->lname }}
                </h1>

                <p class="mt-2 text-sm text-slate-500">
                    Student ID: {{ $student->student_id ?? 'Not provided' }}
                </p>
            </div>

            <div class="rounded-xl bg-slate-50 px-4 py-3 sm:text-right aiq-panel">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                    Completed exams
                </p>
                <p class="mt-1 text-2xl font-bold text-slate-900">
                    {{ $attempts->count() }}
                </p>
            </div>
        </div>
    </header>

    {{-- Status messages --}}
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 aiq-alert aiq-alert-success">
            {{ session('success') }}
        </div>
    @endif

    {-- Render this content conditionally based on the current view state. --}
    @if($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800 aiq-alert aiq-alert-error">
            {{ $errors->first() }}
        </div>
    @endif

    {{-- Overall analysis --}}
    <section class="rounded-2xl border border-slate-200 bg-white p-5 sm:p-6 aiq-panel aiq-panel-interactive">
        <div>
            <h2 class="text-lg font-bold text-slate-900">
                Overall performance analysis
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Review the student’s completed exams from your classes.
            </p>
        </div>

        {-- Collect and submit the data for this operation. --}
        <form
            method="POST"
            action="{{ route('teacher.students.analysis.overall', $student) }}"
            data-ai-url="{{ route('teacher.students.analysis.overall.generate', $student) }}"
            class="mt-5 space-y-3">
            @csrf

            <input
                type="hidden"
                name="method"
                value="manual"
                data-analysis-method>

            <textarea
                name="content"
                rows="5"
                required
                maxlength="10000"
                placeholder="Generate an AI draft or write your overall feedback here."
                class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm text-slate-800 shadow-sm placeholder:text-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100 aiq-field"></textarea>

            <p data-ai-status class="text-sm text-slate-500" aria-live="polite"></p>

            <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                <button
                    type="button"
                    data-generate-ai
                    class="inline-flex justify-center rounded-lg border border-indigo-200 px-4 py-2.5 text-sm font-semibold text-indigo-700 hover:bg-indigo-50 aiq-button aiq-button-secondary">
                    Generate AI Draft
                </button>

                <button
                    type="submit"
                    class="inline-flex justify-center rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-emerald-700 aiq-button aiq-button-success">
                    Send to Student
                </button>
            </div>
        </form>
    </section>

    {{-- Exam history --}}
    <section>
        <div class="mb-4">
            <h2 class="text-lg font-bold text-slate-900">Exam history</h2>
            <p class="mt-1 text-sm text-slate-500">
                Review scores, topic performance, and exam-specific feedback.
            </p>
        </div>

        <div class="space-y-5">
            {-- Iterate over the related records and render each result. --}
            @forelse($attempts as $attempt)
                @php
                    $totalScore = (float) ($attempt->total_score ?? 0);
                    $score = (float) ($attempt->score ?? 0);
                    $percentage = $totalScore > 0
                        ? ($score / $totalScore) * 100
                        : 0;

                    $topicStats = $topicStatsByAttempt->get($attempt->id, collect());

                    $strengthTopics = $topicStats->filter(
                        fn ($topic) => $topic['percentage'] >= 75
                    );

                    $reviewTopics = $topicStats->filter(
                        fn ($topic) => $topic['percentage'] < 75
                    );
                @endphp

                <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white aiq-panel aiq-panel-interactive">
                    {{-- Exam summary --}}
                    <div class="flex flex-col gap-5 p-5 sm:flex-row sm:items-start sm:justify-between sm:p-6">
                        <div class="min-w-0">
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                {{ $attempt->exam?->subject?->name ?? 'No subject' }}
                            </p>

                            <h3 class="mt-1 text-lg font-bold text-slate-900">
                                {{ $attempt->exam?->title ?? 'Exam unavailable' }}
                            </h3>

                            <p class="mt-1 text-sm text-slate-500">
                                Submitted
                                {{ $attempt->submitted_at?->format('M d, Y g:i A') ?? 'Date unavailable' }}
                            </p>
                        </div>

                        <div class="flex flex-wrap items-center gap-4 sm:justify-end">
                            <div class="rounded-xl bg-slate-50 px-4 py-3 aiq-panel">
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Score
                                </p>
                                <p class="mt-1 font-bold text-slate-900">
                                    {{ $attempt->score ?? 0 }} / {{ $attempt->total_score ?? 0 }}
                                </p>
                                <p class="text-sm font-semibold text-indigo-700">
                                    {{ number_format($percentage, 1) }}%
                                </p>
                            </div>

                            <a
                                href="{{ route('teacher.students.attempt-result', $attempt) }}"
                                class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 aiq-button aiq-button-secondary">
                                View student result →
                            </a>
                        </div>
                    </div>

                    {{-- Retry controls --}}
                    @if(in_array($attempt->id, $latestAttemptIds, true))
                        <div class="flex flex-wrap items-center gap-3 border-t border-slate-100 bg-slate-50 px-5 py-3 sm:px-6">
                            {-- Render this content conditionally based on the current view state. --}
                            @if($attempt->retake_allowed)
                                <span class="text-sm font-semibold text-emerald-700">
                                    Retry already allowed
                                </span>
                            {-- Render this content conditionally based on the current view state. --}
                            @else
                                {-- Collect and submit the data for this operation. --}
                                <form
                                    method="POST"
                                    action="{{ route('teacher.students.attempts.reset', $attempt) }}"
                                    onsubmit="return confirm('Allow this student to take this exam again? Their previous result will remain in history.');">
                                    @csrf

                                    <button
                                        type="submit"
                                        class="text-sm font-semibold text-amber-700 hover:text-amber-900 aiq-button aiq-button-secondary">
                                        Reset / Allow Retry
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endif

                    {{-- Strengths and areas for review --}}
                    <div class="grid gap-4 border-t border-slate-100 p-5 sm:p-6 md:grid-cols-2">
                        {-- Render this meaningful page or content section. --}
                        <section class="rounded-xl border border-emerald-100 bg-emerald-50/50 p-4 aiq-panel-interactive">
                            <h4 class="text-sm font-bold text-emerald-800">Strengths</h4>

                            {-- Iterate over the related records and render each result. --}
                            @forelse($strengthTopics as $topic)
                                <div class="mt-3 flex items-start justify-between gap-3 text-sm">
                                    <span class="font-medium text-slate-800">
                                        {{ $topic['topic'] }}
                                    </span>
                                    <span class="shrink-0 whitespace-nowrap text-emerald-700">
                                        {{ $topic['correct'] }}/{{ $topic['total'] }}
                                        · {{ number_format($topic['percentage'], 1) }}%
                                    </span>
                                </div>
                            @empty
                                <p class="mt-2 text-sm text-slate-500">
                                    No topics reached 75% on this exam.
                                </p>
                            @endforelse
                        </section>

                        {-- Render this meaningful page or content section. --}
                        <section class="rounded-xl border border-amber-100 bg-amber-50/50 p-4 aiq-panel-interactive">
                            <h4 class="text-sm font-bold text-amber-800">
                                Areas for review
                            </h4>

                            {-- Iterate over the related records and render each result. --}
                            @forelse($reviewTopics as $topic)
                                <div class="mt-3 flex items-start justify-between gap-3 text-sm">
                                    <span class="font-medium text-slate-800">
                                        {{ $topic['topic'] }}
                                    </span>
                                    <span class="shrink-0 whitespace-nowrap text-amber-700">
                                        {{ $topic['correct'] }}/{{ $topic['total'] }}
                                        · {{ number_format($topic['percentage'], 1) }}%
                                    </span>
                                </div>
                            @empty
                                <p class="mt-2 text-sm text-slate-500">
                                    No topics fell below 75% on this exam.
                                </p>
                            @endforelse
                        </section>

                        {-- Render this content conditionally based on the current view state. --}
                        @if($topicStats->isEmpty())
                            <p class="text-sm text-slate-500 md:col-span-2">
                                Topic analysis isn’t available because this exam’s questions don’t have topic labels.
                            </p>
                        @endif
                    </div>

                    {{-- Exam-specific feedback form --}}
                    <div class="border-t border-slate-100 p-5 sm:p-6">
                        <h4 class="text-sm font-bold text-slate-900">
                            Send feedback for this exam
                        </h4>

                        {-- Collect and submit the data for this operation. --}
                        <form
                            method="POST"
                            action="{{ route('teacher.students.analysis.exam', [$student, $attempt]) }}"
                            data-ai-url="{{ route('teacher.students.analysis.exam.generate', [$student, $attempt]) }}"
                            class="mt-3 space-y-3">
                            @csrf

                            <input
                                type="hidden"
                                name="method"
                                value="manual"
                                data-analysis-method>

                            <textarea
                                name="content"
                                rows="4"
                                required
                                maxlength="10000"
                                placeholder="Generate an AI draft or write feedback for this exam here."
                                class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm text-slate-800 shadow-sm placeholder:text-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100 aiq-field"></textarea>

                            <p data-ai-status class="text-sm text-slate-500" aria-live="polite"></p>

                            <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                                <button
                                    type="button"
                                    data-generate-ai
                                    class="inline-flex justify-center rounded-lg border border-indigo-200 px-4 py-2.5 text-sm font-semibold text-indigo-700 hover:bg-indigo-50 aiq-button aiq-button-secondary">
                                    Generate AI Draft
                                </button>

                                <button
                                    type="submit"
                                    class="inline-flex justify-center rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-emerald-700 aiq-button aiq-button-success">
                                    Send to Student
                                </button>
                            </div>
                        </form>
                    </div>
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center aiq-panel">
                    <p class="text-sm font-medium text-slate-700">
                        No completed exams from your classes yet.
                    </p>
                </div>
            @endforelse
        </div>
    </section>

    {{-- Saved analyses and sent feedback --}}
    <section>
        <div class="mb-4">
            <h2 class="text-lg font-bold text-slate-900">
                Analysis drafts and sent feedback
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Review saved drafts or see feedback already sent to the student.
            </p>
        </div>

        <div class="space-y-4">
            {-- Iterate over the related records and render each result. --}
            @forelse($analyses as $analysis)
                <article class="rounded-2xl border border-slate-200 bg-white p-5 sm:p-6 aiq-panel aiq-panel-interactive">
                    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                        <strong class="text-sm text-slate-900">
                            {{ $analysis->scope === 'overall'
                                ? 'Overall student analysis'
                                : ($analysis->attempt?->exam?->title ?? 'Exam analysis') }}
                        </strong>

                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $analysis->sent_at
                            ? 'bg-emerald-50 text-emerald-700'
                            : 'bg-amber-50 text-amber-700' }}>
                            {{ $analysis->sent_at ? 'Sent to student' : 'Private draft' }}
                        </span>
                    </div>

                    {-- Render this content conditionally based on the current view state. --}
                    @if(!$analysis->sent_at)
                        {-- Collect and submit the data for this operation. --}
                        <form
                            method="POST"
                            action="{{ route('teacher.performance-analyses.update', $analysis) }}"
                            class="space-y-3">
                            @csrf
                            @method('PATCH')

                            <textarea
                                name="content"
                                rows="5"
                                required
                                maxlength="10000"
                                class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm text-slate-800 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100 aiq-field">{{ $analysis->content }}</textarea>

                            <button
                                type="submit"
                                class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 aiq-button aiq-button-secondary">
                                Save Changes
                            </button>
                        </form>

                        {-- Collect and submit the data for this operation. --}
                        <form
                            method="POST"
                            action="{{ route('teacher.performance-analyses.send', $analysis) }}"
                            class="mt-3">
                            @csrf

                            <button
                                type="submit"
                                class="rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-emerald-700 aiq-button aiq-button-success">
                                Send to Student
                            </button>
                        </form>
                    {-- Render this content conditionally based on the current view state. --}
                    @else
                        <p class="whitespace-pre-line text-sm leading-6 text-slate-700">
                            {{ $analysis->content }}
                        </p>

                        <p class="mt-3 text-xs text-slate-400">
                            Sent {{ $analysis->sent_at->format('M d, Y g:i A') }}
                        </p>
                    @endif
                </article>
            @empty
                <p class="rounded-xl border border-slate-200 bg-white p-5 text-sm text-slate-500 aiq-panel">
                    No analyses created yet.
                </p>
            @endforelse
        </div>
    </section>

    <script>
        document.addEventListener('click', async function (event) {
            const button = event.target.closest('[data-generate-ai]');
            if (!button) return;

            const form = button.closest('form');
            const textarea = form.querySelector('textarea[name="content"]');
            const method = form.querySelector('[data-analysis-method]');
            const status = form.querySelector('[data-ai-status]');
            const token = form.querySelector('input[name="_token"]')?.value;

            button.disabled = true;
            button.textContent = 'Generating…';

            if (status) {
                status.textContent = 'Generating draft. Please wait.';
            }

            try {
                const response = await fetch(form.dataset.aiUrl, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': token
                    }
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Could not generate the AI draft.');
                }

                textarea.value = data.content;
                textarea.dispatchEvent(new Event('input', { bubbles: true }));

                if (method) {
                    method.value = 'ai';
                }

                if (status) {
                    status.textContent = 'Draft added. Review or edit it, then send it to the student.';
                }
            } catch (error) {
                if (status) {
                    status.textContent = error.message;
                }
            } finally {
                button.disabled = false;
                button.textContent = 'Generate AI Draft';
            }
        });
    </script>
</div>
@endsection