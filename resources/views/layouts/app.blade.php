<!DOCTYPE html>
<html lang="en" class="h-full bg-[#f7f8fb]">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'AI-Q')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] {
            display: none !important;
        }

        body {
            font-family: 'Inter', sans-serif;
        }

        @keyframes pageEnter {
            from {
                opacity: 0;
                transform: translateY(8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .page-enter {
            animation: pageEnter .35s ease-out both;
        }
    </style>
</head>

<body class="min-h-full bg-[#f7f8fb] text-slate-900 antialiased">

    @auth

    <div
        x-data="{
        mobileMenu: false,
        profileMenu: false
    }"
        class="min-h-screen">

        {{-- ========================================================= --}}
        {{-- GLOBAL COMMAND BAR                                        --}}
        {{-- ========================================================= --}}

        <header
            class="sticky top-0 z-50
               border-b border-slate-200/80
               bg-white/90 backdrop-blur-xl">

            <div class="mx-auto max-w-[1500px] px-4 sm:px-6 lg:px-8">

                {{-- Top Row --}}
                <div class="flex h-[72px] items-center justify-between">

                    {{-- Brand --}}
                    <div class="flex items-center gap-8">

                        <a
                            href="{{ auth()->user()->role === 'teacher'
                            ? url('/teacher/dashboard')
                            : (auth()->user()->role === 'student'
                                ? url('/student/dashboard')
                                : url('/admin/dashboard')) }}"
                            class="group flex items-center gap-3">

                            <div
                                class="relative flex h-10 w-10
                                   items-center justify-center
                                   overflow-hidden rounded-xl
                                   bg-slate-950 text-white
                                   shadow-sm
                                   transition-all duration-300
                                   group-hover:-translate-y-0.5
                                   group-hover:rotate-3
                                   group-hover:shadow-lg
                                   group-active:scale-90">

                                <span class="text-sm font-black tracking-tight">
                                    AQ
                                </span>

                                <div
                                    class="absolute inset-x-0 bottom-0 h-1
                                       bg-indigo-500
                                       transition-all duration-300
                                       group-hover:h-1.5"></div>

                            </div>

                            <div class="hidden sm:block">

                                <div class="flex items-center gap-2">

                                    <span
                                        class="text-lg font-extrabold
                                           tracking-[-0.04em]
                                           text-slate-950">
                                        AI-Q
                                    </span>

                                    <span
                                        class="rounded-md bg-indigo-50
                                           px-1.5 py-0.5
                                           text-[9px] font-extrabold
                                           uppercase tracking-widest
                                           text-indigo-600">
                                        Academic
                                    </span>

                                </div>

                                <p
                                    class="text-[10px] font-semibold
                                       tracking-wide text-slate-400">
                                    Examination Intelligence
                                </p>

                            </div>

                        </a>

                    </div>


                    {{-- Desktop Navigation --}}
                    <nav
                        class="hidden lg:flex
                           h-full items-center gap-1">

                        @if(auth()->user()->role === 'teacher')

                        {{-- Overview --}}
                        <a
                            href="{{ url('/teacher/dashboard') }}"
                            class="relative flex h-full items-center
                                   px-4 text-sm font-semibold
                                   transition-all duration-200
                                   hover:text-indigo-600
                                   active:scale-95
                                   {{ request()->is('teacher/dashboard')
                                        ? 'text-slate-950'
                                        : 'text-slate-500' }}">
                            Overview

                            @if(request()->is('teacher/dashboard'))
                            <span
                                class="absolute inset-x-4 bottom-0
                                           h-[3px] rounded-t-full
                                           bg-indigo-600"></span>
                            @endif
                        </a>


                        {{-- Exams --}}
                        <a
                            href="{{ url('/teacher/exams') }}"
                            class="relative flex h-full items-center
                                   px-4 text-sm font-semibold
                                   transition-all duration-200
                                   hover:text-indigo-600
                                   active:scale-95
                                   {{ request()->is('teacher/exams*')
                                        ? 'text-slate-950'
                                        : 'text-slate-500' }}">
                            Exams

                            @if(request()->is('teacher/exams*'))
                            <span
                                class="absolute inset-x-4 bottom-0
                                           h-[3px] rounded-t-full
                                           bg-indigo-600"></span>
                            @endif
                        </a>


                        {{-- Classes --}}
                        <a
                            href="{{ route('teacher.class-subjects.index') }}"
                            class="relative flex h-full items-center
                                   px-4 text-sm font-semibold
                                   transition-all duration-200
                                   hover:text-indigo-600
                                   active:scale-95
                                   {{ request()->routeIs('teacher.class-subjects*')
                                        ? 'text-slate-950'
                                        : 'text-slate-500' }}">
                            Classes

                            @if(request()->routeIs('teacher.class-subjects*'))
                            <span
                                class="absolute inset-x-4 bottom-0
                                           h-[3px] rounded-t-full
                                           bg-indigo-600"></span>
                            @endif
                        </a>


                        {{-- Analytics --}}
                        <a
                            href="{{ route('teacher.analytics.index') }}"
                            class="relative flex h-full items-center
                                   px-4 text-sm font-semibold
                                   transition-all duration-200
                                   hover:text-indigo-600
                                   active:scale-95
                                   {{ request()->routeIs('teacher.analytics*')
                                        ? 'text-slate-950'
                                        : 'text-slate-500' }}">
                            Analytics

                            @if(request()->routeIs('teacher.analytics*'))
                            <span
                                class="absolute inset-x-4 bottom-0
                                           h-[3px] rounded-t-full
                                           bg-indigo-600"></span>
                            @endif
                        </a>


                        <!-- {{-- AI placeholder --}}
                        <button
                            type="button"
                            class="group relative flex h-full
                                   cursor-default items-center gap-2
                                   px-4 text-sm font-semibold
                                   text-slate-400">

                            AI Insights

                            <span
                                class="rounded-full bg-indigo-50
                                       px-2 py-0.5
                                       text-[9px] font-extrabold
                                       uppercase tracking-wider
                                       text-indigo-500
                                       transition-transform
                                       group-hover:scale-105">
                                Soon
                            </span> -->

                        </button>

                        @elseif(auth()->user()->role === 'student')

                        <a
                            href="{{ url('/student/dashboard') }}"
                            class="relative flex h-full items-center
                                   px-4 text-sm font-semibold
                                   text-slate-950">
                            Overview

                            @if(request()->is('student/dashboard'))
                            <span
                                class="absolute inset-x-4 bottom-0
                                           h-[3px] rounded-t-full
                                           bg-indigo-600"></span>
                            @endif
                        </a>

                        <a
                            href="{{ url('/student/exam') }}"
                            class="relative flex h-full items-center
                                   px-4 text-sm font-semibold
                                   text-slate-500
                                   transition hover:text-indigo-600
                                   active:scale-95">
                            Take Exam
                        </a>

                        <a
                            href="{{ url('/student/dashboard') }}#performance"
                            class="relative flex h-full items-center
                                   px-4 text-sm font-semibold
                                   text-slate-500
                                   transition hover:text-indigo-600
                                   active:scale-95">
                            Performance
                        </a>

                        <!-- <span
                            class="flex h-full items-center
                                   gap-2 px-4
                                   text-sm font-semibold
                                   text-slate-400">
                            AI Insights

                            <span
                                class="rounded-full bg-indigo-50
                                       px-2 py-0.5
                                       text-[9px] font-extrabold
                                       uppercase tracking-wider
                                       text-indigo-500">
                                Soon
                            </span>
                        </span> -->

                        @elseif(auth()->user()->role === 'admin')

                        <a
                            href="{{ url('/admin/dashboard') }}"
                            class="relative flex h-full items-center
                                   px-4 text-sm font-semibold
                                   text-slate-950">
                            Overview

                            <span
                                class="absolute inset-x-4 bottom-0
                                       h-[3px] rounded-t-full
                                       bg-indigo-600"></span>
                        </a>

                        @endif

                    </nav>


                    {{-- Right Commands --}}
                    <div class="flex items-center gap-2">

                        {{-- Teacher Create Exam --}}
                        @if(auth()->user()->role === 'teacher')

                        <a
                            href="{{ url('/teacher/exams') }}"
                            class="group hidden sm:inline-flex
                                   items-center gap-2 rounded-xl
                                   bg-slate-950 px-4 py-2.5
                                   text-xs font-bold text-white
                                   shadow-sm
                                   transition-all duration-200
                                   hover:-translate-y-0.5
                                   hover:bg-indigo-600
                                   hover:shadow-lg
                                   hover:shadow-indigo-200
                                   active:translate-y-0
                                   active:scale-95">

                            <svg
                                class="h-4 w-4
                                       transition-transform duration-300
                                       group-hover:rotate-90
                                       group-hover:scale-110"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2.3"
                                    d="M12 4v16m8-8H4" />
                            </svg>

                            Create Exam

                        </a>

                        @endif


                        {{-- Profile --}}
                        <div class="relative">

                            <button
                                type="button"
                                @click="profileMenu = !profileMenu"
                                @click.outside="profileMenu = false"
                                class="group flex items-center gap-2
                                   rounded-xl p-1.5 pr-2
                                   transition-all duration-200
                                   hover:bg-slate-100
                                   active:scale-95">

                                <div
                                    class="flex h-9 w-9
                                       items-center justify-center
                                       rounded-xl
                                       bg-indigo-50
                                       text-xs font-extrabold
                                       text-indigo-700
                                       ring-1 ring-indigo-100
                                       transition-all duration-300
                                       group-hover:rotate-3
                                       group-hover:bg-indigo-100">
                                    {{ strtoupper(substr(auth()->user()->fname, 0, 1)) }}
                                    {{ strtoupper(substr(auth()->user()->lname, 0, 1)) }}
                                </div>

                                <svg
                                    class="hidden h-4 w-4 text-slate-400
                                       transition-transform duration-200
                                       sm:block"
                                    :class="profileMenu ? 'rotate-180' : ''"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>

                            </button>


                            {{-- Profile Dropdown --}}
                            <div
                                x-cloak
                                x-show="profileMenu"
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                                class="absolute right-0 mt-3 w-64
                                   overflow-hidden rounded-2xl
                                   border border-slate-200
                                   bg-white shadow-2xl
                                   shadow-slate-200/70">

                                <div class="border-b border-slate-100 p-4">

                                    <p
                                        class="text-sm font-bold
                                           text-slate-900">
                                        {{ auth()->user()->fname }}
                                        {{ auth()->user()->lname }}
                                    </p>

                                    <p
                                        class="mt-0.5 text-xs
                                           capitalize text-slate-400">
                                        {{ auth()->user()->role }} account
                                    </p>

                                </div>

                                <div class="p-2">

                                    <form
                                        method="POST"
                                        action="{{ route('logout') }}">
                                        @csrf

                                        <button
                                            type="submit"
                                            class="group flex w-full
                                               items-center gap-3
                                               rounded-xl px-3 py-2.5
                                               text-left text-sm
                                               font-semibold
                                               text-slate-600
                                               transition-all
                                               hover:bg-rose-50
                                               hover:text-rose-600
                                               active:scale-[0.98]">

                                            <svg
                                                class="h-4 w-4
                                                   transition-transform
                                                   group-hover:translate-x-0.5"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M17 16l4-4m0 0-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                            </svg>

                                            Sign out

                                        </button>
                                    </form>

                                </div>

                            </div>

                        </div>


                        {{-- Mobile Menu --}}
                        <button
                            type="button"
                            @click="mobileMenu = !mobileMenu"
                            class="flex h-10 w-10
                               items-center justify-center
                               rounded-xl text-slate-600
                               transition-all
                               hover:bg-slate-100
                               active:scale-90
                               lg:hidden">

                            <svg
                                x-show="!mobileMenu"
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                            </svg>

                            <svg
                                x-cloak
                                x-show="mobileMenu"
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>

                        </button>

                    </div>

                </div>


                {{-- Mobile Navigation --}}
                <div
                    x-cloak
                    x-show="mobileMenu"
                    x-transition
                    class="border-t border-slate-100 py-3 lg:hidden">

                    <div class="grid gap-1">

                        @if(auth()->user()->role === 'teacher')

                        <a
                            href="{{ url('/teacher/dashboard') }}"
                            class="rounded-xl px-4 py-3
                                   text-sm font-semibold
                                   transition-all
                                   hover:bg-slate-100
                                   active:scale-[0.98]">
                            Overview
                        </a>

                        <a
                            href="{{ url('/teacher/exams') }}"
                            class="rounded-xl px-4 py-3
                                   text-sm font-semibold
                                   transition-all
                                   hover:bg-slate-100
                                   active:scale-[0.98]">
                            Exams
                        </a>

                        <a
                            href="{{ route('teacher.class-subjects.index') }}"
                            class="rounded-xl px-4 py-3
                                   text-sm font-semibold
                                   transition-all
                                   hover:bg-slate-100
                                   active:scale-[0.98]">
                            Classes & Subjects
                        </a>

                        <a
                            href="{{ route('teacher.analytics.index') }}"
                            class="rounded-xl px-4 py-3
                                   text-sm font-semibold
                                   transition-all
                                   hover:bg-slate-100
                                   active:scale-[0.98]">
                            Analytics
                        </a>

                        @elseif(auth()->user()->role === 'student')

                        <a
                            href="{{ url('/student/dashboard') }}"
                            class="rounded-xl px-4 py-3
                                   text-sm font-semibold
                                   transition hover:bg-slate-100">
                            Overview
                        </a>

                        <a
                            href="{{ url('/student/exam') }}"
                            class="rounded-xl px-4 py-3
                                   text-sm font-semibold
                                   transition hover:bg-slate-100">
                            Take Exam
                        </a>

                        @else

                        <a
                            href="{{ url('/admin/dashboard') }}"
                            class="rounded-xl px-4 py-3
                                   text-sm font-semibold
                                   transition hover:bg-slate-100">
                            Overview
                        </a>

                        @endif

                    </div>

                </div>

            </div>

        </header>


        {{-- ========================================================= --}}
        {{-- PAGE                                                       --}}
        {{-- ========================================================= --}}

        <main class="page-enter">

            {{-- Context Header --}}
            @hasSection('page-header')

            @yield('page-header')

            @else

            <div
                class="border-b border-slate-200/70
                       bg-white">

                <div
                    class="mx-auto max-w-[1500px]
                           px-4 py-6
                           sm:px-6 lg:px-8">

                    <div
                        class="flex flex-col gap-1
                               sm:flex-row
                               sm:items-end
                               sm:justify-between">

                        <div>

                            <p
                                class="text-[10px] font-extrabold
                                       uppercase tracking-[0.2em]
                                       text-indigo-600">
                                @yield('eyebrow', 'AI-Q Workspace')
                            </p>

                            <h1
                                class="mt-1 text-2xl font-extrabold
                                       tracking-[-0.035em]
                                       text-slate-950">
                                @yield('title', 'Command Center')
                            </h1>

                        </div>

                        @hasSection('page-actions')

                        <div class="mt-4 sm:mt-0">
                            @yield('page-actions')
                        </div>

                        @endif

                    </div>

                </div>

            </div>

            @endif


            {{-- Actual page --}}
            <section
                class="mx-auto max-w-[1500px]
                   px-4 py-7
                   sm:px-6
                   lg:px-8 lg:py-9">

                {{-- Success Toast --}}
                @if(session('success'))

                <div
                    x-data="{ show: true }"
                    x-init="setTimeout(() => show = false, 3500)"
                    x-show="show"
                    x-transition
                    class="fixed right-6 top-24 z-[60]
                           flex max-w-sm items-center gap-3
                           rounded-2xl
                           border border-emerald-200
                           bg-white px-4 py-3
                           shadow-2xl
                           shadow-slate-300/50">

                    <div
                        class="flex h-8 w-8 shrink-0
                               items-center justify-center
                               rounded-full
                               bg-emerald-100
                               text-emerald-600">
                        ✓
                    </div>

                    <p
                        class="text-sm font-semibold
                               text-slate-700">
                        {{ session('success') }}
                    </p>

                    <button
                        type="button"
                        @click="show = false"
                        class="ml-2 text-slate-400
                               transition hover:text-slate-700
                               active:scale-75">
                        ×
                    </button>

                </div>

                @endif


                {{-- Error Toast --}}
                @if(session('error'))

                <div
                    x-data="{ show: true }"
                    x-init="setTimeout(() => show = false, 4500)"
                    x-show="show"
                    x-transition
                    class="fixed right-6 top-24 z-[60]
                           flex max-w-sm items-center gap-3
                           rounded-2xl
                           border border-rose-200
                           bg-white px-4 py-3
                           shadow-2xl">

                    <div
                        class="flex h-8 w-8 shrink-0
                               items-center justify-center
                               rounded-full
                               bg-rose-100 text-rose-600">
                        !
                    </div>

                    <p
                        class="text-sm font-semibold
                               text-slate-700">
                        {{ session('error') }}
                    </p>

                    <button
                        @click="show = false"
                        type="button"
                        class="ml-2 text-slate-400
                               hover:text-slate-700">
                        ×
                    </button>

                </div>

                @endif


                @yield('content')

            </section>

        </main>

    </div>

    @else

    {{-- Guest pages --}}
    <main class="min-h-screen">
        @yield('content')
    </main>

    @endauth


    @stack('scripts')

</body>

</html>