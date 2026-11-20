@extends('layouts.app')

@section('title', 'Class Management | AI-Q')
@section('page-title', 'Class Management')

@section('content')
<div class="space-y-6" x-data="{ createOpen: false, editClass: null }">

    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">
                Class Management
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                Manage classes, teachers, subjects, and enrollment.
            </p>
        </div>
        <!-- <button type="button" @click="createOpen = true"
            class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white">
            + Add Class
        </button> -->
    </div>

    @if(session('success'))
        <div class="rounded-xl bg-green-50 p-4 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="rounded-xl bg-red-50 p-4 text-sm text-red-700">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

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
                        <div class="flex justify-end gap-3">
                            <a href="{{ route('admin.classes.show', $class) }}"
                                class="font-semibold text-indigo-600 hover:underline">View</a>
                            <button type="button"
                                @click="editClass = {{ Illuminate\Support\Js::from([
                                    'id' => $class->id,
                                    'name' => $class->name,
                                    'subject_id' => $class->subject_id,
                                    'created_by' => $class->created_by,
                                ]) }}"
                                class="font-semibold text-slate-700 hover:underline">Edit</button>
                            <form method="POST" action="{{ route('admin.classes.destroy', $class) }}" onsubmit="return confirm('Delete this class?')">
                                @csrf
                                @method('DELETE')
                                <button class="font-semibold text-red-600 hover:underline">Delete</button>
                            </form>
                        </div>
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

    <div x-show="createOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 p-4">
        <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl">
            <div class="mb-5 flex items-center justify-between"><h2 class="text-lg font-bold">Create Class</h2><button type="button" @click="createOpen = false">✕</button></div>
            <form method="POST" action="{{ route('admin.classes.store') }}" class="space-y-4">
                @csrf
                <input name="name" required maxlength="255" placeholder="Class name" class="w-full rounded-xl border-slate-300">
                <select name="subject_id" class="w-full rounded-xl border-slate-300">
                    <option value="">No subject</option>
                    @foreach($subjects as $subject)<option value="{{ $subject->id }}">{{ $subject->name }}</option>@endforeach
                </select>
                <select name="created_by" required class="w-full rounded-xl border-slate-300">
                    <option value="">Select teacher</option>
                    @foreach($teachers as $teacher)<option value="{{ $teacher->id }}">{{ $teacher->fname }} {{ $teacher->lname }}</option>@endforeach
                </select>
                <button class="w-full rounded-xl bg-indigo-600 py-3 font-semibold text-white">Create Class</button>
            </form>
        </div>
    </div>

    <div x-show="editClass" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 p-4">
        <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl">
            <div class="mb-5 flex items-center justify-between"><h2 class="text-lg font-bold">Edit Class</h2><button type="button" @click="editClass = null">✕</button></div>
            <template x-if="editClass">
                <form method="POST" :action="'{{ url('/admin/classes') }}/' + editClass.id" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    <input name="name" x-model="editClass.name" required maxlength="255" class="w-full rounded-xl border-slate-300">
                    <select name="subject_id" x-model="editClass.subject_id" class="w-full rounded-xl border-slate-300">
                        <option value="">No subject</option>
                        @foreach($subjects as $subject)<option value="{{ $subject->id }}">{{ $subject->name }}</option>@endforeach
                    </select>
                    <select name="created_by" x-model="editClass.created_by" required class="w-full rounded-xl border-slate-300">
                        @foreach($teachers as $teacher)<option value="{{ $teacher->id }}">{{ $teacher->fname }} {{ $teacher->lname }}</option>@endforeach
                    </select>
                    <button class="w-full rounded-xl bg-indigo-600 py-3 font-semibold text-white">Save Changes</button>
                </form>
            </template>
        </div>
    </div>

</div>
@endsection