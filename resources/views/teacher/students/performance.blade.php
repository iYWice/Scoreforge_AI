@extends('layouts.app')

@section('title', 'Student Performance')
@section('eyebrow', 'Student Review')

@section('content')
<div class="mx-auto max-w-5xl space-y-8">

    <header class="border-b border-slate-200 pb-6">
        <a href="{{ route('teacher.analytics.index') }}"
           class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">
            ← Back to analytics
        </a>

        <h1 class="mt-4 text-2xl font-extrabold text-slate-950">
            {{ $student->fname }} {{ $student->lname }}
        </h1>

        <p class="mt-1 text-sm text-slate-500">
            Student ID: {{ $student->student_id ?? 'Not provided' }}
            · {{ $attempts->count() }} completed exam(s)
        </p>
    </header>

    @if(session('success'))
        <div class="rounded-lg bg-emerald-50 p-3 text-sm text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-lg bg-rose-50 p-3 text-sm text-rose-700">
            {{ $errors->first() }}
        </div>
    @endif

    <section class="rounded-xl border border-slate-200 bg-white p-6">
        <h2 class="text-lg font-bold text-slate-900">Overall performance analysis</h2>
        <p class="mt-1 text-sm text-slate-500">
            Analyze the student’s completed exams from your classes.
        </p>

        <form
    method="POST"
    action="{{ route('teacher.students.analysis.overall', $student) }}"
    data-ai-url="{{ route('teacher.students.analysis.overall.generate', $student) }}"
    class="mt-4 space-y-3">
    @csrf

    <input type="hidden" name="method" value="manual" data-analysis-method>

    <textarea
        name="content"
        rows="4"
        required
        maxlength="10000"
        placeholder="Generate an AI draft or write your overall feedback here."
        class="w-full rounded-lg border-slate-300"></textarea>

    <p data-ai-status class="text-sm text-slate-500"></p>

    <div class="flex flex-wrap gap-3">
        <button
            type="button"
            data-generate-ai
            class="rounded-lg border border-indigo-200 px-4 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-50">
            Generate AI Draft
        </button>

        <button
            type="submit"
            class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-bold text-white hover:bg-emerald-700">
            Send to Student
        </button>
    </div>
</form>
    </section>

    <section>
        <h2 class="mb-4 text-lg font-bold text-slate-900">Exam history</h2>

        <div class="space-y-5">
            @forelse($attempts as $attempt)
                @php
                    $percentage = ($attempt->total_score ?? 0) > 0
                        ? ($attempt->score / $attempt->total_score) * 100
                        : 0;
                @endphp

                <article class="rounded-xl border border-slate-200 bg-white p-5">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <h3 class="font-bold text-slate-900">
                                {{ $attempt->exam?->title ?? 'Exam unavailable' }}
                            </h3>
                            <p class="mt-1 text-sm text-slate-500">
                                {{ $attempt->exam?->subject?->name ?? 'No subject' }}
                                · {{ $attempt->submitted_at?->format('M d, Y g:i A') ?? 'Date unavailable' }}
                            </p>
                        </div>

                        <div class="text-right">
                            <p class="font-bold text-slate-900">
                                {{ $attempt->score }} / {{ $attempt->total_score }}
                            </p>
                            <p class="text-sm text-slate-500">
                                {{ number_format($percentage, 1) }}%
                            </p>
    <a
        href="{{ route('teacher.students.attempt-result', $attempt) }}"
        class="mt-2 inline-block text-xs font-semibold text-indigo-600 hover:text-indigo-800">
        View student result →
    </a>

                        </div>
                    </div>
@php
    $topicStats = $topicStatsByAttempt->get($attempt->id, collect());

    $strengthTopics = $topicStats->filter(
        fn ($topic) => $topic['percentage'] >= 75
    );

    $reviewTopics = $topicStats->filter(
        fn ($topic) => $topic['percentage'] < 75
    );
@endphp

<div class="mt-4 grid gap-4 border-t border-slate-100 pt-4 md:grid-cols-2">
    <section class="rounded-lg border border-emerald-100 bg-emerald-50/50 p-4">
        <h4 class="text-sm font-bold text-emerald-800">Strengths</h4>

        @forelse($strengthTopics as $topic)
            <div class="mt-3 flex justify-between gap-3 text-sm">
                <span class="font-medium text-slate-800">
                    {{ $topic['topic'] }}
                </span>
                <span class="whitespace-nowrap text-emerald-700">
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

    <section class="rounded-lg border border-amber-100 bg-amber-50/50 p-4">
        <h4 class="text-sm font-bold text-amber-800">Areas for review</h4>

        @forelse($reviewTopics as $topic)
            <div class="mt-3 flex justify-between gap-3 text-sm">
                <span class="font-medium text-slate-800">
                    {{ $topic['topic'] }}
                </span>
                <span class="whitespace-nowrap text-amber-700">
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
</div>

@if($topicStats->isEmpty())
    <p class="mt-3 text-xs text-slate-500">
        Topic analysis isn’t available because this exam’s questions don’t have topic labels.
    </p>
@endif
                    <form
    method="POST"
    action="{{ route('teacher.students.analysis.exam', [$student, $attempt]) }}"
    data-ai-url="{{ route('teacher.students.analysis.exam.generate', [$student, $attempt]) }}"
    class="mt-4 space-y-3 border-t border-slate-100 pt-4">
    @csrf

    <input type="hidden" name="method" value="manual">

    <textarea
        name="content"
        rows="4"
        required
        maxlength="10000"
        placeholder="Generate an AI draft or write feedback for this exam here."
        class="w-full rounded-lg border-slate-300"></textarea>

    <p data-ai-status class="text-sm text-slate-500"></p>

    <div class="flex flex-wrap gap-3">
        <button
            type="button"
            data-generate-ai
            class="rounded-lg border border-indigo-200 px-4 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-50">
            Generate AI Draft
        </button>

        <button
            type="submit"
            class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-bold text-white hover:bg-emerald-700">
            Send to Student
        </button>
    </div>
</form>
                </article>
            @empty
                <p class="rounded-xl border border-dashed border-slate-300 p-8 text-center text-sm text-slate-500">
                    No completed exams from your classes yet.
                </p>
            @endforelse
        </div>
    </section>

    <section>
        <h2 class="mb-4 text-lg font-bold text-slate-900">Analysis drafts and sent feedback</h2>

        <div class="space-y-4">
            @forelse($analyses as $analysis)
                <article class="rounded-xl border border-slate-200 bg-white p-5">
                    <div class="mb-3 flex flex-wrap justify-between gap-2 text-sm">
                        <strong class="text-slate-900">
                            {{ $analysis->scope === 'overall'
                                ? 'Overall student analysis'
                                : ($analysis->attempt?->exam?->title ?? 'Exam analysis') }}
                        </strong>

                        <span class="{{ $analysis->sent_at ? 'text-emerald-700' : 'text-amber-700' }}">
                            {{ $analysis->sent_at ? 'Sent to student' : 'Private draft' }}
                        </span>
                    </div>

                    @if(!$analysis->sent_at)
                        <form method="POST"
                              action="{{ route('teacher.performance-analyses.update', $analysis) }}"
                              class="space-y-3">
                            @csrf
                            @method('PATCH')

                            <textarea name="content" rows="5" required maxlength="10000"
                                      class="w-full rounded-lg border-slate-300">{{ $analysis->content }}</textarea>

                            <button class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                                Save Changes
                            </button>
                        </form>

                        <form method="POST"
                              action="{{ route('teacher.performance-analyses.send', $analysis) }}"
                              class="mt-3">
                            @csrf
                            <button class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-bold text-white hover:bg-emerald-700">
                                Send to Student
                            </button>
                        </form>
                    @else
                        <p class="whitespace-pre-line text-sm leading-6 text-slate-700">
                            {{ $analysis->content }}
                        </p>
                        <p class="mt-2 text-xs text-slate-400">
                            Sent {{ $analysis->sent_at->format('M d, Y g:i A') }}
                        </p>
                    @endif
                </article>
            @empty
                <p class="text-sm text-slate-500">No analyses created yet.</p>
            @endforelse
        </div>
    </section>
</div>
@endsection
<script>
document.addEventListener('click', async function (event) {
    const button = event.target.closest('[data-generate-ai]');
    if (!button) return;

    const form = button.closest('form');
    const textarea = form.querySelector('textarea[name="content"]');
    const method = form.querySelector('[data-analysis-method]');
    const status = form.querySelector('[data-ai-status]');

    button.disabled = true;
    button.textContent = 'Generating…';
    status.textContent = 'Generating draft. Please wait.';

    try {
        const response = await fetch(form.dataset.aiUrl, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value
            }
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'Could not generate the AI draft.');
        }

        textarea.value = data.content;
        textarea.dispatchEvent(new Event('input', { bubbles: true }));
        method.value = 'ai';
        status.textContent = 'Draft added. Review or edit it, then send it to the student.';
    } catch (error) {
        status.textContent = error.message;
    } finally {
        button.disabled = false;
        button.textContent = 'Generate AI Draft';
    }
});
</script>