@extends('layouts.app')

@section('title', 'Subject Management | AI-Q')
@section('page-title', 'Subject Management')

@section('content')
<div class="space-y-6" x-data="{ createOpen: false, editSubject: null }">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Subjects</h1>
            <p class="mt-1 text-sm text-slate-500">Manage subjects available to classes and examinations.</p>
        </div>
        <!-- <button type="button" @click="createOpen = true"
            class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white">+ Add Subject</button> -->
    </div>

    @if(session('success'))
        <div class="rounded-xl bg-green-50 p-4 text-sm text-green-700">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="rounded-xl bg-red-50 p-4 text-sm text-red-700">
            <ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="GET" action="{{ route('admin.subjects.index') }}" class="flex gap-3">
        <input type="search" name="search" value="{{ request('search') }}" placeholder="Search subjects..."
            class="w-full rounded-xl border-slate-300 text-sm">
        <button class="rounded-xl bg-slate-900 px-5 py-2 text-sm font-medium text-white">Search</button>
    </form>

    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="px-5 py-4">Subject</th>
                    <th class="px-5 py-4">Classes</th>
                    <th class="px-5 py-4">Exams</th>
                    <th class="px-5 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($subjects as $subject)
                <tr>
                    <td class="px-5 py-4 font-semibold text-slate-900">{{ $subject->name }}</td>
                    <td class="px-5 py-4">{{ $subject->class_subjects_count }}</td>
                    <td class="px-5 py-4">{{ $subject->exams_count }}</td>
                    <td class="px-5 py-4">
                        <div class="flex justify-end gap-3">
                            <button type="button" @click="editSubject = {{ Illuminate\Support\Js::from(['id' => $subject->id, 'name' => $subject->name]) }}"
                                class="font-medium text-indigo-600 hover:underline">Edit</button>
                            <form method="POST" action="{{ route('admin.subjects.destroy', $subject) }}" onsubmit="return confirm('Delete this subject?')">
                                @csrf
                                @method('DELETE')
                                <button class="font-medium text-red-600 hover:underline">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-5 py-10 text-center text-slate-500">No subjects found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $subjects->links() }}

    <div x-show="createOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 p-4">
        <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl">
            <div class="mb-5 flex items-center justify-between"><h2 class="text-lg font-bold">Create Subject</h2><button type="button" @click="createOpen = false">✕</button></div>
            <form method="POST" action="{{ route('admin.subjects.store') }}" class="space-y-4">
                @csrf
                <input name="name" required maxlength="255" placeholder="Subject name" class="w-full rounded-xl border-slate-300">
                <button class="w-full rounded-xl bg-indigo-600 py-3 font-semibold text-white">Create Subject</button>
            </form>
        </div>
    </div>

    <div x-show="editSubject" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 p-4">
        <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl">
            <div class="mb-5 flex items-center justify-between"><h2 class="text-lg font-bold">Edit Subject</h2><button type="button" @click="editSubject = null">✕</button></div>
            <template x-if="editSubject">
                <form method="POST" :action="'{{ url('/admin/subjects') }}/' + editSubject.id" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    <input name="name" x-model="editSubject.name" required maxlength="255" class="w-full rounded-xl border-slate-300">
                    <button class="w-full rounded-xl bg-indigo-600 py-3 font-semibold text-white">Save Changes</button>
                </form>
            </template>
        </div>
    </div>
</div>
@endsection