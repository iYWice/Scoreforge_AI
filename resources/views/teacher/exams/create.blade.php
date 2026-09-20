<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Exam</title>

    {{-- Inter Font CDN for refined academic typography --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-slate-50 dark:bg-slate-900 font-sans antialiased text-slate-800 dark:text-slate-100 flex items-center justify-center min-h-screen p-4 sm:p-6">

    <div class="w-full sm:max-w-lg bg-white dark:bg-slate-800 p-8 rounded-2xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-200/80 dark:border-slate-700/80 transition-all duration-300">

        {{-- HEADER SECTION --}}
        <div class="mb-8 text-center">
            <div class="inline-flex items-center justify-center w-12 h-12 bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 rounded-xl mb-3 border border-indigo-100 dark:border-indigo-800/50 shadow-sm">
                {{-- Academic Assessment Icon --}}
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-slate-100 tracking-tight">
                {{ __('Create New Exam') }}
            </h1>
            <p class="mt-1.5 text-xs sm:text-sm text-slate-500 dark:text-slate-400">
                {{ __('Set up the parameters and rules for your new assessment.') }}
            </p>
        </div>

        <form method="POST" action="/teacher/exams" class="space-y-5">
            @csrf

            {{-- EXAM TITLE --}}
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1.5">Exam Title</label>
                <input
                    type="text"
                    name="title"
                    required
                    placeholder="e.g., Midterm Examination"
                    class="w-full px-4 py-2.5 border border-slate-300 dark:border-slate-700 rounded-xl bg-slate-50/50 dark:bg-slate-900/60 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-600 dark:focus:border-indigo-500 outline-none transition-all duration-200 ease-[cubic-bezier(0.34,1.56,0.64,1)] hover:border-slate-400 hover:scale-[1.01] focus:scale-[1.01]">
            </div>

            {{-- SUBJECT & CLASS GRID --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- SUBJECT --}}
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1.5">Subject</label>
                    <select
                        name="subject_id"
                        required
                        class="w-full px-4 py-2.5 border border-slate-300 dark:border-slate-700 rounded-xl bg-slate-50/50 dark:bg-slate-900/60 text-slate-900 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-600 dark:focus:border-indigo-500 outline-none transition-all duration-200 ease-[cubic-bezier(0.34,1.56,0.64,1)] hover:border-slate-400 hover:scale-[1.01] focus:scale-[1.01] cursor-pointer">
                        <option value="" disabled selected>Select Subject</option>
                        @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}">
                            {{ $subject->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- CLASS --}}
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1.5">Target Class</label>
                    <select
                        name="class_id"
                        required
                        class="w-full px-4 py-2.5 border border-slate-300 dark:border-slate-700 rounded-xl bg-slate-50/50 dark:bg-slate-900/60 text-slate-900 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-600 dark:focus:border-indigo-500 outline-none transition-all duration-200 ease-[cubic-bezier(0.34,1.56,0.64,1)] hover:border-slate-400 hover:scale-[1.01] focus:scale-[1.01] cursor-pointer">
                        <option value="" disabled selected>Select Class</option>
                        @foreach($classes as $class)
                        <option value="{{ $class->id }}">
                            {{ $class->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- DURATION & PASSING SCORE GRID --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- DURATION --}}
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1.5">Duration (mins)</label>
                    <input
                        type="number"
                        name="duration"
                        min="1"
                        required
                        placeholder="e.g., 60"
                        class="w-full px-4 py-2.5 border border-slate-300 dark:border-slate-700 rounded-xl bg-slate-50/50 dark:bg-slate-900/60 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-600 dark:focus:border-indigo-500 outline-none transition-all duration-200 ease-[cubic-bezier(0.34,1.56,0.64,1)] hover:border-slate-400 hover:scale-[1.01] focus:scale-[1.01]">
                </div>

                {{-- PASSING SCORE --}}
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1.5">Passing Score</label>
                    <input
                        type="number"
                        name="passing_score"
                        min="0"
                        required
                        placeholder="e.g., 70"
                        class="w-full px-4 py-2.5 border border-slate-300 dark:border-slate-700 rounded-xl bg-slate-50/50 dark:bg-slate-900/60 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-600 dark:focus:border-indigo-500 outline-none transition-all duration-200 ease-[cubic-bezier(0.34,1.56,0.64,1)] hover:border-slate-400 hover:scale-[1.01] focus:scale-[1.01]">
                </div>
            </div>

            {{-- ACTION BUTTON --}}
            <div class="pt-3">
                <button
                    type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-semibold py-3 px-4 rounded-xl shadow-md shadow-indigo-500/20 hover:shadow-lg hover:shadow-indigo-500/30 transition-all duration-200 ease-[cubic-bezier(0.34,1.56,0.64,1)] hover:scale-[1.02] active:scale-95 cursor-pointer text-center text-sm tracking-wide flex items-center justify-center gap-2 group">
                    <span>Create Exam</span>
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform duration-200 ease-out" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
            </div>
        </form>
    </div>

</body>

</html>