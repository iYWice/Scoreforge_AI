@extends('layouts.app')

@section('title', 'Exam Management | AI-Q')
@section('page-title', 'Examination Management')

@section('content')
<div class="space-y-6 aiq-exam-management" x-data="{ createOpen: false, editExam: null }">

    <div class="flex flex-wrap items-center justify-between gap-4 aiq-panel aiq-panel-interactive p-5 sm:p-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">
                Examination Management
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                Monitor all examination records and publication statuses.
            </p>
        </div>
        <!-- <button type="button" @click="createOpen = true"
            class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white">
            + Add Examination
        </button> -->
    </div>

    @if(session('success'))
        <div class="rounded-xl bg-green-50 p-4 text-sm text-green-700 aiq-alert aiq-alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="rounded-xl bg-red-50 p-4 text-sm text-red-700 aiq-alert aiq-alert-error">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="GET" action="{{ route('admin.exams.index') }}"
        class="grid min-w-0 gap-3 sm:grid-cols-2 lg:grid-cols-[minmax(0,1fr)_190px_auto] sm:items-center aiq-panel p-4">
        <input type="search" name="search"
            value="{{ request('search') }}"
            placeholder="Search exam, code or teacher..."
            class="w-full min-w-0 rounded-xl border-slate-300 text-sm sm:col-span-2 lg:col-span-1 aiq-field">

        <select name="status" class="w-full rounded-xl border-slate-300 text-sm aiq-field">
            <option value="">All Statuses</option>
            @foreach(['draft', 'published', 'closed'] as $status)
            <option value="{{ $status }}"
                @selected(request('status')===$status)>
                {{ ucfirst($status) }}
            </option>
            @endforeach
        </select>

        <button class="rounded-xl bg-slate-900 px-5 py-2 text-sm font-medium text-white aiq-button aiq-button-secondary">
            Filter
        </button>
    </form>

    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white aiq-panel">
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
                        {{ $exam->class?->name ?? 'Unassigned' }}
                    </td>
                    <td class="px-5 py-4">{{ $exam->questions_count }}</td>
                    <td class="px-5 py-4">{{ $exam->attempts_count }}</td>
                    <td class="px-5 py-4">
                        <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold capitalize text-indigo-700 aiq-badge">
                            {{ $exam->status }}
                        </span>
                    </td>
                    <td class="px-5 py-4 text-right">
                        <div class="flex flex-wrap justify-end gap-2">
                            <a href="{{ route('admin.exams.show', $exam) }}"
                                class="font-semibold text-indigo-600 hover:underline aiq-button aiq-button-secondary">View</a>
                            <button type="button"
                                @click="editExam = {{ Illuminate\Support\Js::from([
                                    'id' => $exam->id,
                                    'title' => $exam->title,
                                    'subject_id' => $exam->subject_id,
                                    'class_id' => $exam->class_id,
                                    'created_by' => $exam->created_by,
                                    'total_items' => $exam->total_items,
                                    'duration' => $exam->duration,
                                    'passing_score' => $exam->passing_score,
                                    'status' => $exam->status,
                                ]) }}"
                                class="font-semibold text-slate-700 hover:underline aiq-button aiq-button-secondary">Edit</button>
                            <form method="POST" action="{{ route('admin.exams.destroy', $exam) }}" onsubmit="return confirm('Delete this examination?')">
                                @csrf
                                @method('DELETE')
                                <button class="font-semibold text-red-600 hover:underline aiq-button aiq-button-danger">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-10 text-center text-slate-500 aiq-panel">
                        No examinations found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $exams->links() }}

<div x-show="createOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-slate-950/50 p-4">
        <div class="my-auto max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white p-6 shadow-xl aiq-panel">
            <div class="mb-5 flex items-center justify-between"><h2 class="text-lg font-bold">Create Examination</h2><button type="button" @click="createOpen = false" class="aiq-button aiq-button-secondary" style="width:40px;min-width:40px;height:40px;min-height:40px;padding:0">✕</button></div>
            <form method="POST" action="{{ route('admin.exams.store') }}" class="grid gap-4 sm:grid-cols-2">
                @csrf
                <input name="title" required maxlength="255" placeholder="Exam title" class="sm:col-span-2 w-full rounded-xl border-slate-300 aiq-field">
                <select name="subject_id" required class="w-full rounded-xl border-slate-300 aiq-field"><option value="">Subject</option>@foreach($subjects as $subject)<option value="{{ $subject->id }}">{{ $subject->name }}</option>@endforeach</select>
                <select name="class_id" required class="w-full rounded-xl border-slate-300 aiq-field"><option value="">Class</option>@foreach($classes as $class)<option value="{{ $class->id }}">{{ $class->name }}</option>@endforeach</select>
                <select name="created_by" required class="sm:col-span-2 w-full rounded-xl border-slate-300 aiq-field"><option value="">Teacher</option>@foreach($teachers as $teacher)<option value="{{ $teacher->id }}">{{ $teacher->fname }} {{ $teacher->lname }}</option>@endforeach</select>
                <input name="total_items" type="number" min="1" required placeholder="Total items" class="w-full rounded-xl border-slate-300 aiq-field">
                <input name="duration" type="number" min="1" required value="60" placeholder="Duration (minutes)" class="w-full rounded-xl border-slate-300 aiq-field">
                <input name="passing_score" type="number" min="0" max="100" required value="75" placeholder="Passing score (%)" class="w-full rounded-xl border-slate-300 aiq-field">
                <select name="status" required class="w-full rounded-xl border-slate-300 aiq-field"><option value="draft">Draft</option><option value="published">Published</option><option value="closed">Closed</option></select>
                <button class="sm:col-span-2 w-full rounded-xl bg-indigo-600 py-3 font-semibold text-white aiq-button aiq-button-primary">Create Examination</button>
            </form>
        </div>
    </div>

    <div x-show="editExam" x-cloak class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-slate-950/50 p-4">
        <div class="my-auto max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white p-6 shadow-xl aiq-panel">
            <div class="mb-5 flex items-center justify-between"><h2 class="text-lg font-bold">Edit Examination</h2><button type="button" @click="editExam = null" class="aiq-button aiq-button-secondary" style="width:40px;min-width:40px;height:40px;min-height:40px;padding:0">✕</button></div>
            <template x-if="editExam">
                <form method="POST" :action="'{{ url('/admin/exams') }}/' + editExam.id" class="grid gap-4 sm:grid-cols-2">
                    @csrf
                    @method('PATCH')
                    <input name="title" x-model="editExam.title" required maxlength="255" class="sm:col-span-2 w-full rounded-xl border-slate-300 aiq-field">
                    <select name="subject_id" x-model="editExam.subject_id" required class="w-full rounded-xl border-slate-300 aiq-field">@foreach($subjects as $subject)<option value="{{ $subject->id }}">{{ $subject->name }}</option>@endforeach</select>
                    <select name="class_id" x-model="editExam.class_id" required class="w-full rounded-xl border-slate-300 aiq-field">@foreach($classes as $class)<option value="{{ $class->id }}">{{ $class->name }}</option>@endforeach</select>
                    <select name="created_by" x-model="editExam.created_by" required class="sm:col-span-2 w-full rounded-xl border-slate-300 aiq-field">@foreach($teachers as $teacher)<option value="{{ $teacher->id }}">{{ $teacher->fname }} {{ $teacher->lname }}</option>@endforeach</select>
                    <input name="total_items" type="number" min="1" x-model="editExam.total_items" required class="w-full rounded-xl border-slate-300 aiq-field">
                    <input name="duration" type="number" min="1" x-model="editExam.duration" required class="w-full rounded-xl border-slate-300 aiq-field">
                    <input name="passing_score" type="number" min="0" max="100" x-model="editExam.passing_score" required class="w-full rounded-xl border-slate-300 aiq-field">
                    <select name="status" x-model="editExam.status" required class="w-full rounded-xl border-slate-300 aiq-field"><option value="draft">Draft</option><option value="published">Published</option><option value="closed">Closed</option></select>
                    <button class="sm:col-span-2 w-full rounded-xl bg-indigo-600 py-3 font-semibold text-white aiq-button aiq-button-primary">Save Changes</button>
                </form>
            </template>
        </div>
    </div>
</div>
@endsection