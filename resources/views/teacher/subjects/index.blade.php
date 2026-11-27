@extends('layouts.app')

@section('title', 'Subjects')
@section('eyebrow', 'Teacher Workspace')

@section('content')
<div class="space-y-8 aiq-subject-management">
    <header class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between aiq-panel aiq-panel-interactive p-5 sm:p-6">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-slate-950">Subjects</h1>
            <p class="mt-1 text-sm text-slate-500">
                Create subject offerings and manage student enrollment.
            </p>
        </div>
        <span class="w-fit rounded-full border border-indigo-100 bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-700 aiq-badge">
            {{ $offerings->count() }} {{ Str::plural('subject', $offerings->count()) }}
        </span>
    </header>

    @if ($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-700 aiq-alert aiq-alert-error" role="alert">
            <p class="font-bold">Please check the form</p>
            <ul class="mt-2 list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm aiq-panel aiq-panel-interactive">
        <div class="border-b border-slate-100 px-5 py-5 sm:px-6">
            <h2 class="text-lg font-bold text-slate-950">Create Subject</h2>
            <p class="mt-1 text-sm text-slate-500">Add the offering details and choose enrolled students.</p>
        </div>

        <form id="create-subject-form" method="POST" action="{{ route('teacher.subjects.store') }}" class="p-5 sm:p-6">
            @csrf

            <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_320px]">
                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label for="subject_name" class="text-sm font-semibold text-slate-700">Subject name</label>
                        <input
                            id="subject_name"
                            name="subject_name"
                            type="text"
                            value="{{ old('subject_name') }}"
                            placeholder="e.g. Database Management"
                            required
                            class="mt-2 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 aiq-field">
                    </div>

                    <div>
                        <label for="edp_code" class="text-sm font-semibold text-slate-700">EDP code</label>
                        <div class="mt-2 flex min-w-0 flex-wrap gap-2 sm:flex-nowrap">
                            <input
                                id="edp_code"
                                name="edp_code"
                                type="text"
                                inputmode="numeric"
                                pattern="[0-9]{6}"
                                maxlength="6"
                                value="{{ old('edp_code') }}"
                                placeholder="e.g. 123456"
                                required
                                class="min-w-0 flex-1 rounded-xl border-slate-300 font-mono text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 aiq-field">
                            <button
                                type="button"
                                id="generate-edp-code"
                                class="shrink-0 rounded-xl border border-indigo-200 bg-indigo-50 px-3 py-2 text-sm font-bold text-indigo-700 transition hover:bg-indigo-100 active:scale-[0.98] aiq-button aiq-button-secondary">
                                Randomize
                            </button>
                        </div>
                        <p class="mt-1 text-xs text-slate-400">Must contain exactly 6 digits.</p>
                    </div>

                    <fieldset class="md:col-span-2">
                        <legend class="text-sm font-semibold text-slate-700">Class days</legend>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach (['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $day)
                                <label class="cursor-pointer">
                                    <input
                                        type="checkbox"
                                        name="schedule_days[]"
                                        value="{{ $day }}"
                                        @checked(in_array($day, old('schedule_days', [])))
                                        class="peer sr-only aiq-check">
                                    <span class="inline-flex min-w-14 justify-center rounded-xl border border-slate-200 px-3 py-2.5 text-sm font-semibold text-slate-600 transition peer-checked:border-indigo-600 peer-checked:bg-indigo-50 peer-checked:text-indigo-700">
                                        {{ $day }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        @error('schedule_days')
                            <p class="mt-2 text-xs font-medium text-rose-600">{{ $message }}</p>
                        @enderror
                    </fieldset>

                    <div>
                        <label for="start_time" class="text-sm font-semibold text-slate-700">Start time</label>
                        <input
                            id="start_time"
                            name="start_time"
                            type="time"
                            value="{{ old('start_time') }}"
                            required
                            class="mt-2 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 aiq-field">
                    </div>

                    <div>
                        <label for="end_time" class="text-sm font-semibold text-slate-700">End time</label>
                        <input
                            id="end_time"
                            name="end_time"
                            type="time"
                            value="{{ old('end_time') }}"
                            required
                            class="mt-2 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 aiq-field">
                    </div>

                    <div class="md:col-span-2">
                        <label for="room" class="text-sm font-semibold text-slate-700">
                            Room <span class="font-normal text-slate-400">(optional)</span>
                        </label>
                        <input
                            id="room"
                            name="room"
                            type="text"
                            value="{{ old('room') }}"
                            placeholder="e.g. Room 205"
                            class="mt-2 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 aiq-field">
                    </div>

                    <div>
                        <label for="class_id" class="text-sm font-semibold text-slate-700">Class</label>
                        <select
                            id="class_id"
                            name="class_id"
                            required
                            class="mt-2 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 aiq-field">
                            <option value="">Select a class</option>
                            @foreach ($classes as $class)
                                <option value="{{ $class->id }}" @selected(old('class_id') == $class->id)>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('class_id')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex min-h-64 flex-col">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-slate-700">Select students</h3>
                        <span class="student-count text-xs font-medium text-slate-400">0 available</span>
                    </div>

                    <div class="mt-2 max-h-72 flex-1 overflow-y-auto rounded-xl border border-slate-200">
                        @forelse ($students as $student)
                            @php
                                $studentClassIds = $classes
                                    ->filter(fn ($class) => $class->students->contains('id', $student->id))
                                    ->pluck('id')
                                    ->implode(',');
                            @endphp
                            <label data-student-option data-class-ids="{{ $studentClassIds }}" style="display: none;" class="flex cursor-pointer items-center gap-3 border-b border-slate-100 px-4 py-3 transition last:border-0 hover:bg-slate-50 aiq-panel-interactive">
                                <input
                                    type="checkbox"
                                    name="student_ids[]"
                                    value="{{ $student->id }}"
                                    @checked(in_array($student->id, old('student_ids', [])))
                                    class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 aiq-check">
                                <span class="min-w-0 text-sm">
                                    <span class="block truncate font-semibold text-slate-800">{{ $student->fname }} {{ $student->lname }}</span>
                                    <span class="block truncate text-xs text-slate-400">{{ $student->email }}</span>
                                </span>
                            </label>
                        @empty
                            <p class="px-4 py-8 text-center text-sm text-slate-500">No student accounts yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end border-t border-slate-100 pt-5">
                <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-indigo-700 active:scale-[0.98] aiq-button aiq-button-primary">
                    Create subject
                </button>
            </div>
        </form>
    </section>

    <section class="space-y-4">
        <div>
            <h2 class="text-lg font-bold text-slate-950">My Subjects</h2>
            <p class="mt-1 text-sm text-slate-500">Review offerings and update student enrollment.</p>
        </div>

        @forelse ($offerings as $offering)
            <article
                x-data="{ editing: false }"
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm aiq-panel aiq-panel-interactive">
                <div class="flex items-center justify-end px-5 pt-4">
                    <button
                        type="button"
                        @click="editing = !editing"
                        class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-bold text-slate-700 transition hover:bg-slate-50 aiq-button aiq-button-secondary">
                        <span x-text="editing ? 'Cancel editing' : 'Edit subject'"></span>
                    </button>
                </div>

                <form
                    x-show="editing"
                    x-cloak
                    method="POST"
                    action="{{ route('teacher.subjects.update', $offering) }}"
                    class="grid gap-4 border-b border-slate-100 p-5 md:grid-cols-2">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label for="subject-name-{{ $offering->id }}" class="text-sm font-semibold text-slate-700">Subject name</label>
                        <input
                            id="subject-name-{{ $offering->id }}"
                            name="subject_name"
                            type="text"
                            value="{{ old('subject_name', $offering->subject->name) }}"
                            required
                            class="mt-2 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 aiq-field">
                    </div>

                    <div>
                        <label for="edp-code-{{ $offering->id }}" class="text-sm font-semibold text-slate-700">EDP code</label>
                        <input
                            id="edp-code-{{ $offering->id }}"
                            name="edp_code"
                            type="text"
                            inputmode="numeric"
                            maxlength="6"
                            value="{{ old('edp_code', $offering->edp_code) }}"
                            required
                            class="mt-2 w-full rounded-xl border-slate-300 font-mono text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 aiq-field">
                    </div>

                    <div>
                        <label for="edit-class-{{ $offering->id }}" class="text-sm font-semibold text-slate-700">Class</label>
                        <select
                            id="edit-class-{{ $offering->id }}"
                            name="class_id"
                            required
                            class="mt-2 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 aiq-field">
                            @foreach ($classes as $class)
                                <option value="{{ $class->id }}" @selected(old('class_id', $offering->class_id) == $class->id)>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <fieldset>
                        <legend class="text-sm font-semibold text-slate-700">Class days</legend>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach (['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $day)
                                <label class="cursor-pointer">
                                    <input type="checkbox" name="schedule_days[]" value="{{ $day }}" class="peer sr-only aiq-check">
                                    <span class="inline-flex min-w-12 justify-center rounded-xl border border-slate-200 px-2 py-2 text-xs font-semibold text-slate-600 peer-checked:border-indigo-600 peer-checked:bg-indigo-50 peer-checked:text-indigo-700">{{ $day }}</span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>

                    <div>
                        <label for="start-time-{{ $offering->id }}" class="text-sm font-semibold text-slate-700">Start time</label>
                        <input id="start-time-{{ $offering->id }}" name="start_time" type="time" required class="mt-2 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 aiq-field">
                    </div>

                    <div>
                        <label for="end-time-{{ $offering->id }}" class="text-sm font-semibold text-slate-700">End time</label>
                        <input id="end-time-{{ $offering->id }}" name="end_time" type="time" required class="mt-2 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 aiq-field">
                    </div>

                    <div class="md:col-span-2">
                        <label for="room-{{ $offering->id }}" class="text-sm font-semibold text-slate-700">Room <span class="font-normal text-slate-400">(optional)</span></label>
                        <input id="room-{{ $offering->id }}" name="room" type="text" placeholder="e.g. Room 205" class="mt-2 w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 aiq-field">
                    </div>

                    <div class="md:col-span-2 flex justify-end">
                        <button type="submit" class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-indigo-700 aiq-button aiq-button-primary">
                            Save subject
                        </button>
                    </div>
                </form>

                <form data-student-filter method="POST" action="{{ route('teacher.subjects.students.sync', $offering) }}">
                    @csrf

                    <div class="grid gap-4 border-b border-slate-100 bg-slate-50 p-5 sm:grid-cols-2 xl:grid-cols-4">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Subject</p>
                            <p class="mt-1 font-bold text-slate-900">{{ $offering->subject->name }}</p>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">EDP code</p>
                            <p class="mt-1 font-mono font-semibold text-slate-800">{{ $offering->edp_code }}</p>
                        </div>
                        <div>
                            <label for="class-{{ $offering->id }}" class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Class</label>
                            <select
                                id="class-{{ $offering->id }}"
                                name="class_id"
                                required
                                class="mt-1 w-full rounded-xl border-slate-300 bg-white text-sm font-semibold text-slate-800 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 aiq-field">
                                @foreach ($classes as $class)
                                    <option value="{{ $class->id }}" @selected((int) $offering->class_id === (int) $class->id)>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Schedule</p>
                            <p class="mt-1 font-semibold text-slate-800">{{ $offering->schedule }}</p>
                        </div>
                    </div>

                    <div class="p-5">
                    <div class="flex items-center justify-between gap-3">
                        <h3 class="text-sm font-bold text-slate-800">Enrolled students</h3>
                        <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600 aiq-badge">
                            {{ $offering->students_count }} enrolled
                        </span>
                    </div>

                    <div class="mt-3 grid max-h-72 gap-2 overflow-y-auto sm:grid-cols-2 lg:grid-cols-3">
                        @forelse ($students as $student)
                            @php
                                $studentClassIds = $classes
                                    ->filter(fn ($class) => $class->students->contains('id', $student->id))
                                    ->pluck('id')
                                    ->implode(',');
                            @endphp
                            <label data-student-option data-class-ids="{{ $studentClassIds }}" style="display: none;" class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 px-3 py-3 transition hover:border-indigo-200 hover:bg-indigo-50/40 aiq-panel-interactive">
                                <input
                                    type="checkbox"
                                    name="student_ids[]"
                                    value="{{ $student->id }}"
                                    @checked($offering->students->contains('id', $student->id))
                                    class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 aiq-check">
                                <span class="min-w-0 text-sm">
                                    <span class="block truncate font-semibold text-slate-800">{{ $student->fname }} {{ $student->lname }}</span>
                                    <span class="block truncate text-xs text-slate-400">{{ $student->email }}</span>
                                </span>
                            </label>
                        @empty
                            <p class="text-sm text-slate-500">There are no student accounts yet.</p>
                        @endforelse
                    </div>

                    <div class="mt-5 flex flex-col gap-3 border-t border-slate-100 pt-4 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-xs text-slate-500">Unchecked students will be removed from this subject.</p>
                        <button type="submit" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-50 active:scale-[0.98] aiq-button aiq-button-success">
                            Save changes
                        </button>
                    </div>
                    </div>
                </form>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-10 text-center aiq-panel">
                <p class="font-bold text-slate-800">No subjects created yet</p>
                <p class="mt-1 text-sm text-slate-500">Use the form above to create your first subject.</p>
            </div>
        @endforelse
    </section>
</div>
@endsection

@push('scripts')
    <script>
        function filterStudents(form) {
            const classSelect = form.querySelector('[name="class_id"]');
            const options = [...form.querySelectorAll('[data-student-option]')];
            let visibleCount = 0;

            options.forEach((option) => {
                const checkbox = option.querySelector('input[type="checkbox"]');
                const classIds = (option.dataset.classIds || '').split(',');
                const isEligible = classSelect.value !== '' && classIds.includes(classSelect.value);

                option.style.display = isEligible ? '' : 'none';
                checkbox.disabled = !isEligible;

                if (!isEligible) {
                    checkbox.checked = false;
                } else {
                    visibleCount++;
                }
            });

            const count = form.querySelector('.student-count');
            if (count) {
                count.textContent = `${visibleCount} available`;
            }
        }

        document.querySelectorAll('form[data-student-filter]').forEach((form) => {
            const classSelect = form.querySelector('[name="class_id"]');
            classSelect?.addEventListener('change', () => filterStudents(form));
            filterStudents(form);
        });

        const createForm = document.getElementById('create-subject-form');
        const createClassSelect = createForm?.querySelector('[name="class_id"]');
        createClassSelect?.addEventListener('change', () => filterStudents(createForm));
        if (createForm) {
            filterStudents(createForm);
        }

        document.getElementById('generate-edp-code')?.addEventListener('click', function () {
            const edpCode = document.getElementById('edp_code');
            if (edpCode) {
                edpCode.value = String(Math.floor(100000 + Math.random() * 900000));
                edpCode.dispatchEvent(new Event('input', { bubbles: true }));
            }
        });

        document.getElementById('edp_code')?.addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
        });
    </script>
@endpush