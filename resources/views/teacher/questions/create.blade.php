@extends('layouts.app')

@section('title', 'Question Builder')

@section('content')

<div class="max-w-4xl mx-auto space-y-6">

    {{-- Navigation Header --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('teacher.exams.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-indigo-600 dark:text-slate-400 dark:hover:text-indigo-400 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back to Exams
        </a>
        <span class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Question Construction</span>
    </div>

    {{-- Error Banner --}}
    @if($errors->any())
    <div class="bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800 rounded-2xl p-4 transition-colors">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-rose-600 dark:text-rose-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <h3 class="text-xs font-bold text-rose-800 dark:text-rose-300 uppercase tracking-wider">Please fix the following validation errors:</h3>
                <ul class="mt-1.5 list-disc list-inside text-xs text-rose-700 dark:text-rose-400 space-y-1 font-medium">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    {{-- Question Builder Form Card --}}
    <div class="bg-white dark:bg-slate-900 shadow-xl shadow-slate-900/5 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6 sm:p-8 transition-colors">

        <div class="mb-6 pb-6 border-b border-slate-100 dark:border-slate-800 flex items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">
                    Question Builder
                </h1>
                <p class="text-xs font-medium text-slate-400 dark:text-slate-500 mt-1">
                    Exam Target: <span class="text-indigo-600 dark:text-indigo-400 font-semibold">{{ $exam->title }}</span>
                </p>
            </div>
            <span class="px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 text-xs font-semibold shrink-0">
                Draft Mode
            </span>
        </div>

        <form method="POST" action="{{ route('teacher.questions.store', $exam->id) }}" class="space-y-6">
            @csrf

            {{-- Question Type Selection --}}
            <div class="space-y-1.5">
                <label for="questionType" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                    Question Type
                </label>
                <div class="relative">
                    <select
                        id="questionType"
                        name="question_type"
                        class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-xs font-medium text-slate-800 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 focus:outline-none transition-all appearance-none cursor-pointer">
                        <option value="mcq" {{ old('question_type') == 'mcq' ? 'selected' : '' }}>Multiple Choice</option>
                        <option value="tf" {{ old('question_type') == 'tf' ? 'selected' : '' }}>True / False</option>
                        <option value="identification" {{ old('question_type') == 'identification' ? 'selected' : '' }}>Identification</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Question Prompt --}}
            <div class="space-y-1.5">
                <label for="question_text" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                    Question Text / Statement
                </label>
                <textarea
                    id="question_text"
                    name="question_text"
                    rows="3"
                    required
                    placeholder="Enter the main question statement or problem here..."
                    class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-xs font-medium text-slate-800 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 focus:outline-none transition-all placeholder:text-slate-400">{{ old('question_text') }}</textarea>
            </div>
            {{-- Topic --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                    Topic
                </label>

                <input
                    type="text"
                    name="topic"
                    value="{{ old('topic') }}"
                    placeholder="Example: SQL Joins, Normalization, Arrays"
                    class="w-full rounded-xl border-slate-300 dark:border-slate-700
               dark:bg-slate-900 dark:text-white">

                @error('topic')
                <p class="mt-1 text-sm text-red-500">
                    {{ $message }}
                </p>
                @enderror
            </div>

            {{-- Dynamic Section: MCQ Options --}}
            <div id="mcqFields" class="space-y-4 pt-2">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                    Multiple Choice Inputs & Correct Choice Selection
                </label>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <input type="text" name="choice_a" value="{{ old('choice_a') }}" placeholder="Choice A" class="bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-xs font-medium text-slate-800 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 focus:outline-none transition-all">
                    <input type="text" name="choice_b" value="{{ old('choice_b') }}" placeholder="Choice B" class="bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-xs font-medium text-slate-800 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 focus:outline-none transition-all">
                    <input type="text" name="choice_c" value="{{ old('choice_c') }}" placeholder="Choice C" class="bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-xs font-medium text-slate-800 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 focus:outline-none transition-all">
                    <input type="text" name="choice_d" value="{{ old('choice_d') }}" placeholder="Choice D" class="bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2.5 text-xs font-medium text-slate-800 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 focus:outline-none transition-all">
                </div>

                <div class="pt-2">
                    <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-2">Select Radio Choice corresponding to the correct answer:</span>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <label class="mcq-radio-card flex items-center justify-between p-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 cursor-pointer hover:border-indigo-500 transition-all">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Option A</span>
                            <input type="radio" name="correct_answer" id="radioA" value="" class="w-4 h-4 text-indigo-600 border-slate-300 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800">
                        </label>
                        <label class="mcq-radio-card flex items-center justify-between p-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 cursor-pointer hover:border-indigo-500 transition-all">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Option B</span>
                            <input type="radio" name="correct_answer" id="radioB" value="" class="w-4 h-4 text-indigo-600 border-slate-300 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800">
                        </label>
                        <label class="mcq-radio-card flex items-center justify-between p-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 cursor-pointer hover:border-indigo-500 transition-all">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Option C</span>
                            <input type="radio" name="correct_answer" id="radioC" value="" class="w-4 h-4 text-indigo-600 border-slate-300 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800">
                        </label>
                        <label class="mcq-radio-card flex items-center justify-between p-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 cursor-pointer hover:border-indigo-500 transition-all">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Option D</span>
                            <input type="radio" name="correct_answer" id="radioD" value="" class="w-4 h-4 text-indigo-600 border-slate-300 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800">
                        </label>
                    </div>
                </div>
            </div>

            {{-- Dynamic Section: True/False --}}
            <div id="tfFields" class="space-y-2 pt-2" style="display: none;">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                    Correct True/False Statement Key
                </label>
                <div class="grid grid-cols-2 gap-3 max-w-xs">
                    <label class="tf-radio-card flex items-center justify-center p-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 cursor-pointer hover:border-indigo-500 transition-all">
                        <input type="radio" name="correct_answer" value="True" class="w-4 h-4 text-indigo-600 border-slate-300 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800">
                        <span class="ml-2 text-xs font-bold text-slate-700 dark:text-slate-300">True</span>
                    </label>
                    <label class="tf-radio-card flex items-center justify-center p-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40 cursor-pointer hover:border-indigo-500 transition-all">
                        <input type="radio" name="correct_answer" value="False" class="w-4 h-4 text-indigo-600 border-slate-300 focus:ring-indigo-500 dark:border-slate-700 dark:bg-slate-800">
                        <span class="ml-2 text-xs font-bold text-slate-700 dark:text-slate-300">False</span>
                    </label>
                </div>
            </div>

            {{-- Dynamic Section: Identification --}}
            <div id="idFields" class="space-y-1.5 pt-2" style="display: none;">
                <label for="id_correct_answer" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                    Exact Text Answer Key
                </label>
                <input
                    type="text"
                    id="id_correct_answer"
                    name="correct_answer"
                    placeholder="Enter expected text answer string..."
                    class="id-input w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-xs font-medium text-slate-800 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 focus:outline-none transition-all placeholder:text-slate-400">
            </div>

            {{-- Points Assignment --}}
            <div class="space-y-1.5 pt-2">
                <label for="points" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                    Question Score Value
                </label>
                <input
                    type="number"
                    id="points"
                    name="points"
                    value="{{ old('points', 1) }}"
                    min="1"
                    required
                    class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-xs font-medium text-slate-800 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 focus:outline-none transition-all">
            </div>

            {{-- Submission Action --}}
            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100 dark:border-slate-800">
                <button
                    type="submit"
                    class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 text-white font-semibold text-xs px-6 py-3 rounded-xl shadow-lg shadow-indigo-500/20 transition-all duration-200 hover:scale-[1.01] active:scale-95 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Save Question</span>
                </button>
            </div>

        </form>

    </div>

</div>

<script>
    const typeSelect = document.getElementById('questionType');
    const mcqFields = document.getElementById('mcqFields');
    const tfFields = document.getElementById('tfFields');
    const idFields = document.getElementById('idFields');

    function toggleInputState(container, enable) {
        const inputs = container.querySelectorAll('input');
        inputs.forEach(input => {
            input.disabled = !enable;
        });
    }

    function updateUI() {
        // Reset display
        mcqFields.style.display = 'none';
        tfFields.style.display = 'none';
        idFields.style.display = 'none';

        // Disable inputs inside hidden sections so they aren't submitted
        toggleInputState(mcqFields, false);
        toggleInputState(tfFields, false);
        toggleInputState(idFields, false);

        if (typeSelect.value === 'mcq') {
            mcqFields.style.display = 'block';
            toggleInputState(mcqFields, true);
        } else if (typeSelect.value === 'tf') {
            tfFields.style.display = 'block';
            toggleInputState(tfFields, true);
        } else if (typeSelect.value === 'identification') {
            idFields.style.display = 'block';
            toggleInputState(idFields, true);
        }
    }

    typeSelect.addEventListener('change', updateUI);
    updateUI();

    // Dynamically set MCQ radio values from user inputs
    const choiceInputs = [{
            selector: 'input[name=choice_a]',
            targetId: 'radioA'
        },
        {
            selector: 'input[name=choice_b]',
            targetId: 'radioB'
        },
        {
            selector: 'input[name=choice_c]',
            targetId: 'radioC'
        },
        {
            selector: 'input[name=choice_d]',
            targetId: 'radioD'
        }
    ];

    choiceInputs.forEach(item => {
        const inputElem = document.querySelector(item.selector);
        const radioElem = document.getElementById(item.targetId);

        if (inputElem && radioElem) {
            radioElem.value = inputElem.value;
            inputElem.addEventListener('input', (e) => {
                radioElem.value = e.target.value;
            });
        }
    });
</script>

@endsection