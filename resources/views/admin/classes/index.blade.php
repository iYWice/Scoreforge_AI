@extends('layouts.app')

@section('title', 'Class Management | AI-Q')
@section('page-title', 'Class Management')

@section('content')
<div class="space-y-6">

    <div>
        <h1 class="text-2xl font-bold text-slate-900">
            Class Management
        </h1>
        <p class="mt-1 text-sm text-slate-500">
            Monitor classes, teachers, and student enrollment.
        </p>
    </div>

    <form method="GET" action="{{ route('admin.classes.index') }}"
        class="flex gap-3">
        <input type="search" name="search"
            value="{{ request('search') }}"
            placeholder="Search class, code, subject or teacher..."
            class="w-full rounded-xl border-slate-300 text-sm">

        <button class="rounded-xl bg-slate-900 px-5 py-2 text-sm font-medium text-white">
            Search
        </button>
    </form>

    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="px-5 py-4">Class</th>
                    <th class="px-5 py-4">Code</th>
                    <th class="px-5 py-4">Subject</th>
                    <th class="px-5 py-4">Teacher</th>
                    <th class="px-5 py-4">Students</th>
                    <th class="px-5 py-4">Exams</th>
                    <th class="px-5 py-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($classes as $class)
                <tr>
                    <td class="px-5 py-4 font-semibold text-slate-900">
                        {{ $class->name }}
                    </td>
                    <td class="px-5 py-4">
                        <code>{{ $class->class_code }}</code>
                    </td>
                    <td class="px-5 py-4">
                        {{ $class->subject?->name ?? 'Unassigned' }}
                    </td>
                    <td class="px-5 py-4">
                        {{ $class->teacher?->fname }}
                        {{ $class->teacher?->lname }}
                    </td>
                    <td class="px-5 py-4">{{ $class->students_count }}</td>
                    <td class="px-5 py-4">{{ $class->exams_count }}</td>
                    <td class="px-5 py-4 text-right">
                        <a href="{{ route('admin.classes.show', $class) }}"
                            class="font-semibold text-indigo-600 hover:underline">
                            View
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-10 text-center text-slate-500">
                        No classes found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $classes->links() }}

</div>
@endsection