@extends('layouts.app')

@section('title', 'Generated Questions')
@section('eyebrow', 'AI Question Builder')

@section('content')

<div class="mx-auto max-w-5xl">

    <div class="mb-8">

        <a
            href="{{ route(
                'teacher.ai-questions.create',
                $exam
            ) }}"
            class="text-sm font-semibold
                   text-slate-500
                   hover:text-indigo-600">
            ← Back to Question Builder
        </a>

        <h2
            class="mt-5 text-2xl font-bold
                   text-slate-950">
            Generated Draft
        </h2>

        <p class="mt-2 text-sm text-slate-500">
            {{ count(
                $session->generated_questions ?? []
            ) }}
            questions generated from
            {{ $session->source_name }}.
        </p>

    </div>


    <div class="space-y-4">

        @foreach(
        $session->generated_questions ?? []
        as $index => $question
        )

        <article
            class="rounded-2xl border
                       border-slate-200
                       bg-white p-6">

            <div
                class="mb-4 flex items-start
                           justify-between gap-4">

                <h3
                    class="font-semibold
                               text-slate-950">
                    {{ $index + 1 }}.
                    {{ $question['question_text'] }}
                </h3>

                <span
                    class="shrink-0 rounded-full
                               bg-indigo-50 px-3 py-1
                               text-xs font-semibold
                               text-indigo-700">
                    {{
                            str_replace(
                                '_',
                                ' ',
                                $question['question_type']
                            )
                        }}
                </span>

            </div>


            <p
                class="mb-4 text-xs font-semibold
                           uppercase tracking-wide
                           text-slate-400">
                {{ $question['topic'] ?? 'General' }}
            </p>


            @if(
            !empty($question['options'])
            )

            <div class="mb-4 space-y-2">

                @foreach(
                $question['options']
                as $option
                )

                <div
                    class="rounded-lg
                                       bg-slate-50
                                       px-4 py-2
                                       text-sm
                                       text-slate-700">
                    {{ $option }}
                </div>

                @endforeach

            </div>

            @endif


            <div
                class="border-t border-slate-100
                           pt-4 text-sm">
                <span
                    class="font-semibold
                               text-slate-500">
                    Correct Answer:
                </span>

                <span
                    class="ml-1 font-semibold
                               text-emerald-700">
                    {{ $question['correct_answer'] }}
                </span>
            </div>

        </article>

        @endforeach

    </div>

</div>

@endsection