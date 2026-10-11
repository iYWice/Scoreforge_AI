@extends('layouts.app')

@section('title', 'Exam Management | AI-Q')
@section('page-title', 'Examination Management')

@section('content')
<div class="space-y-6">

    <div>
        <h1 class="text-2xl font-bold text-slate-900">
            Examination Management
        </h1>
        <p class="mt-1 text-sm text-slate-500">
            Monitor all examination records and publication statuses.
        </p>
    </div>

    <form method="GET" action="{{ route('admin.exams.index') }}"
        class="flex flex-wrap gap-3">
        <input type="search" name="search"
            value="{{ request('search') }}"
            placeholder="Search exam, code or teacher..."
            class="min-w-52 flex-1 rounded-xl border-slate-300 text-sm">

        <select name="status" class="rounded-xl border-slate-300 text-sm">
            <option value="">All Statuses</option>
            @foreach(['draft', 'published', 'closed'] as $status)
            <option value="{{ $status }}"
                @selected(request('status')===$status)>
                {{ ucfirst($status) }}
            </option>
            @endforeach
        </select>

        <button class="rounded-xl bg-slate-900 px-5 py-2 text-sm font-medium text-white">
            Filter
        </button>
    </form>

    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="px-5 py-4">Examination</th>
                    <th class="px-5 py-4">Teacher</th>
                    <th class="px-5 py-4">Class</th>
                    <th class="px-5 py-4">Questions</th>
                    <th class="px-5 py-4">Attempts</th>
                    <th class="px-5 py-4">Status</th>
                    <th class="px-5 py-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($exams as $exam)
                <tr>
                    <td class="px-5 py-4">
                        <p class="font-semibold text-slate-900">
                            {{ $exam->title }}
                        </p>
                        <p class="text-xs text-slate-500">
                            {{ $exam->exam_code }}
                        </p>
                    </td>
                    <td class="px-5 py-4">
                        {{ $exam->teacher?->fname }}
                        {{ $exam->teacher?->lname }}
                    </td>
                    <td class="px-5 py-4">
                        {{ $exam->schoolClass?->name ?? 'Unassigned' }}
                    </td>
                    <td class="px-5 py-4">{{ $exam->questions_count }}</td>
                    <td class="px-5 py-4">{{ $exam->attempts_count }}</td>
                    <td class="px-5 py-4">
                        <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold capitalize text-indigo-700">
                            {{ $exam->status }}
                        </span>
                    </td>
                    <td class="px-5 py-4 text-right">
                        <a href="{{ route('admin.exams.show', $exam) }}"
                            class="font-semibold text-indigo-600 hover:underline">
                            View
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-10 text-center text-slate-500">
                        No examinations found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $exams->links() }}

</div>
@endsection