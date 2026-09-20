<!DOCTYPE html>

<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    class="h-full">

<head>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1">

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}">

    <title>AI-Q | Examination Intelligence</title>


    <link
        rel="preconnect"
        href="https://fonts.bunny.net">

    <link
        href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800"
        rel="stylesheet">


    @if (
        file_exists(public_path('build/manifest.json'))
        || file_exists(public_path('hot'))
    )

        @vite([
            'resources/css/app.css',
            'resources/js/app.js'
        ])

    @endif

</head>



<body
    class="min-h-full bg-slate-50
           font-sans text-slate-800 antialiased">


<div
    x-data="{
        tab: '{{ $errors->any() && old('role') ? 'register' : 'login' }}',
        loginLoading: false,
        registerLoading: false,
        mobileAuth: false
    }"
    class="flex min-h-screen flex-col">


    {{-- ====================================================== --}}
    {{-- TOP NAVIGATION                                         --}}
    {{-- ====================================================== --}}

    <header
        class="border-b border-slate-200
               bg-white/95 backdrop-blur-xl">

        <div
            class="mx-auto flex h-16 max-w-7xl
                   items-center justify-between
                   px-4 sm:px-6 lg:px-8">


            {{-- Brand --}}
            <a
                href="{{ url('/') }}"
                class="group flex items-center gap-3">

                <div
                    class="flex h-9 w-9 items-center
                           justify-center rounded-lg
                           bg-indigo-600 text-xs
                           font-black text-white
                           shadow-sm transition-all
                           duration-200
                           group-hover:-translate-y-0.5
                           group-hover:shadow-lg
                           group-hover:shadow-indigo-500/20">

                    AQ

                </div>


                <div>

                    <div class="flex items-center gap-2">

                        <span
                            class="text-sm font-black
                                   tracking-[-0.03em]
                                   text-slate-950">

                            AI-Q

                        </span>

                        <span
                            class="hidden text-[8px]
                                   font-extrabold uppercase
                                   tracking-[0.16em]
                                   text-slate-300 sm:inline">

                            Examination Intelligence

                        </span>

                    </div>


                    <p
                        class="text-[8px] font-bold
                               uppercase tracking-[0.13em]
                               text-slate-400">

                        UCLM Academic Platform

                    </p>

                </div>

            </a>



            {{-- Right side --}}
            <div class="flex items-center gap-4">

                @auth

                    <span
                        class="hidden text-xs font-semibold
                               text-slate-500 sm:block">

                        {{ auth()->user()->fname }}
                        {{ auth()->user()->lname }}

                    </span>


                    @php

                        $dashboardRoute = match(auth()->user()->role) {
                            'teacher' => route('teacher.dashboard'),
                            'student' => route('student.dashboard'),
                            default => url('/dashboard'),
                        };

                    @endphp


                    <a
                        href="{{ $dashboardRoute }}"
                        class="group inline-flex items-center
                               gap-2 rounded-xl bg-slate-950
                               px-4 py-2.5 text-xs font-bold
                               text-white transition-all
                               hover:-translate-y-0.5
                               hover:bg-indigo-600
                               active:translate-y-0
                               active:scale-95">

                        Open Workspace

                        <span
                            class="transition-transform
                                   group-hover:translate-x-1">
                            →
                        </span>

                    </a>

                @else

                    <p
                        class="hidden text-[10px] font-semibold
                               text-slate-400 sm:block">

                        Already have an account?

                    </p>


                    <button
                        type="button"
                        @click="
                            tab = 'login';
                            document
                                .getElementById('auth-workspace')
                                .scrollIntoView({
                                    behavior: 'smooth'
                                });
                        "
                        class="rounded-xl border
                               border-slate-200 bg-white
                               px-4 py-2.5 text-xs
                               font-bold text-slate-700
                               transition-all
                               hover:-translate-y-0.5
                               hover:border-indigo-300
                               hover:text-indigo-600
                               active:translate-y-0
                               active:scale-95">

                        Log In

                    </button>

                @endauth

            </div>

        </div>

    </header>



    {{-- ====================================================== --}}
    {{-- MAIN                                                   --}}
    {{-- ====================================================== --}}

    <main class="flex flex-1 items-center">

        <div
            class="mx-auto w-full max-w-7xl
                   px-4 py-10 sm:px-6 sm:py-14
                   lg:px-8 lg:py-16">


            @auth

                {{-- ========================================== --}}
                {{-- AUTHENTICATED WELCOME                      --}}
                {{-- ========================================== --}}

                <section class="mx-auto max-w-4xl">

                    <p
                        class="text-[10px] font-extrabold
                               uppercase tracking-[0.22em]
                               text-indigo-600">

                        AI-Q Workspace

                    </p>


                    <h1
                        class="mt-3 max-w-3xl text-4xl
                               font-black tracking-[-0.065em]
                               text-slate-950 sm:text-5xl
                               lg:text-6xl">

                        Welcome back,
                        {{ auth()->user()->fname }}.

                    </h1>


                    <p
                        class="mt-5 max-w-2xl text-sm
                               leading-7 text-slate-500 sm:text-base">

                        Continue to your AI-Q workspace to manage,
                        take, or analyze examinations based on
                        your account role.

                    </p>


                    <div
                        class="mt-9 flex flex-col gap-3
                               sm:flex-row sm:items-center">

                        <a
                            href="{{ $dashboardRoute }}"
                            class="group inline-flex
                                   items-center justify-center
                                   gap-2 rounded-xl
                                   bg-indigo-600 px-6 py-3
                                   text-xs font-bold text-white
                                   shadow-sm transition-all
                                   duration-200
                                   hover:-translate-y-0.5
                                   hover:bg-indigo-500
                                   hover:shadow-lg
                                   hover:shadow-indigo-500/20
                                   active:translate-y-0
                                   active:scale-95">

                            Enter Command Center

                            <span
                                class="transition-transform
                                       group-hover:translate-x-1">
                                →
                            </span>

                        </a>


                        <form
                            method="POST"
                            action="{{ route('logout') }}">

                            @csrf

                            <button
                                type="submit"
                                class="rounded-xl px-5 py-3
                                       text-xs font-bold
                                       text-slate-500
                                       transition-all
                                       hover:bg-slate-100
                                       hover:text-rose-600
                                       active:scale-95">

                                Log Out

                            </button>

                        </form>

                    </div>

                </section>


            @else

                {{-- ========================================== --}}
                {{-- GUEST WORKSPACE                            --}}
                {{-- ========================================== --}}

                <div
                    class="grid items-center gap-12
                           lg:grid-cols-[minmax(0,1fr)_440px]
                           xl:gap-20">


                    {{-- ====================================== --}}
                    {{-- LEFT: AI-Q IDENTITY                    --}}
                    {{-- ====================================== --}}

                    <section>


                        <div
                            class="inline-flex items-center
                                   gap-2 border-b
                                   border-indigo-200 pb-2">

                            <span
                                class="h-2 w-2 rounded-full
                                       bg-indigo-600">
                            </span>

                            <span
                                class="text-[10px] font-extrabold
                                       uppercase tracking-[0.22em]
                                       text-indigo-600">

                                Examination Management
                                & Analytics

                            </span>

                        </div>



                        <h1
                            class="mt-7 max-w-3xl
                                   text-4xl font-black
                                   leading-[1.03]
                                   tracking-[-0.07em]
                                   text-slate-950
                                   sm:text-5xl
                                   lg:text-[4rem]">

                            Examination data,
                            transformed into

                            <span class="text-indigo-600">
                                academic insight.
                            </span>

                        </h1>



                        <p
                            class="mt-6 max-w-2xl
                                   text-sm leading-7
                                   text-slate-500
                                   sm:text-base">

                            AI-Q is an examination management
                            and analytics platform designed for
                            the University of Cebu Lapu-Lapu
                            and Mandaue. Create and take
                            examinations, process scores
                            automatically, and monitor academic
                            performance from one workspace.

                        </p>



                        {{-- Feature signals --}}
                        <div
                            class="mt-10 grid gap-x-8
                                   gap-y-6 border-y
                                   border-slate-200 py-7
                                   sm:grid-cols-3">


                            <div>

                                <p
                                    class="text-[9px]
                                           font-extrabold uppercase
                                           tracking-[0.16em]
                                           text-slate-400">

                                    Assessment

                                </p>

                                <p
                                    class="mt-2 text-sm
                                           font-extrabold
                                           text-slate-850">

                                    Automated Scoring

                                </p>

                                <p
                                    class="mt-1 text-[11px]
                                           leading-5 text-slate-400">

                                    Process examination
                                    responses and scores.

                                </p>

                            </div>



                            <div
                                class="sm:border-l
                                       sm:border-slate-200
                                       sm:pl-7">

                                <p
                                    class="text-[9px]
                                           font-extrabold uppercase
                                           tracking-[0.16em]
                                           text-slate-400">

                                    Performance

                                </p>

                                <p
                                    class="mt-2 text-sm
                                           font-extrabold
                                           text-slate-850">

                                    Learning Analytics

                                </p>

                                <p
                                    class="mt-1 text-[11px]
                                           leading-5 text-slate-400">

                                    Track scores, trends,
                                    topics, and improvement.

                                </p>

                            </div>



                            <div
                                class="sm:border-l
                                       sm:border-slate-200
                                       sm:pl-7">

                                <p
                                    class="text-[9px]
                                           font-extrabold uppercase
                                           tracking-[0.16em]
                                           text-slate-400">

                                    Intelligence

                                </p>

                                <p
                                    class="mt-2 text-sm
                                           font-extrabold
                                           text-slate-850">

                                    AI-Q Insights

                                </p>

                                <p
                                    class="mt-1 text-[11px]
                                           leading-5 text-slate-400">

                                    AI-assisted academic
                                    interpretation is the
                                    next intelligence layer.

                                </p>

                            </div>

                        </div>



                        {{-- Academic identity --}}
                        <div
                            class="mt-7 flex flex-wrap
                                   items-center gap-x-6 gap-y-3">

                            <p
                                class="text-[9px]
                                       font-extrabold uppercase
                                       tracking-[0.16em]
                                       text-slate-400">

                                Built for

                            </p>

                            <span
                                class="text-xs font-bold
                                       text-slate-600">

                                Teachers

                            </span>

                            <span
                                class="h-1 w-1 rounded-full
                                       bg-slate-300">
                            </span>

                            <span
                                class="text-xs font-bold
                                       text-slate-600">

                                Students

                            </span>

                            <span
                                class="h-1 w-1 rounded-full
                                       bg-slate-300">
                            </span>

                            <span
                                class="text-xs font-bold
                                       text-slate-600">

                                Academic Performance

                            </span>

                        </div>

                    </section>



                    {{-- ====================================== --}}
                    {{-- RIGHT: AUTHENTICATION WORKSPACE        --}}
                    {{-- ====================================== --}}

                    <section
                        id="auth-workspace"
                        class="border border-slate-200
                               bg-white shadow-xl
                               shadow-slate-900/[0.04]">


                        {{-- Header --}}
                        <div
                            class="border-b border-slate-200
                                   px-6 pt-6 sm:px-7">

                            <p
                                class="text-[9px] font-extrabold
                                       uppercase tracking-[0.18em]
                                       text-indigo-600">

                                Account Access

                            </p>


                            <h2
                                class="mt-2 text-xl font-black
                                       tracking-[-0.035em]
                                       text-slate-950">

                                Enter AI-Q

                            </h2>


                            <p
                                class="mt-1 text-xs
                                       leading-5 text-slate-400">

                                Access your examination workspace
                                or create a new account.

                            </p>



                            {{-- Tabs --}}
                            <div class="mt-6 flex gap-6">

                                <button
                                    type="button"
                                    @click="tab = 'login'"
                                    :class="tab === 'login'
                                        ? 'border-indigo-600 text-indigo-600'
                                        : 'border-transparent text-slate-400 hover:text-slate-700'"
                                    class="border-b-2 pb-3
                                           text-[10px] font-extrabold
                                           uppercase tracking-[0.15em]
                                           transition-all
                                           active:scale-95">

                                    Log In

                                </button>


                                <button
                                    type="button"
                                    @click="tab = 'register'"
                                    :class="tab === 'register'
                                        ? 'border-indigo-600 text-indigo-600'
                                        : 'border-transparent text-slate-400 hover:text-slate-700'"
                                    class="border-b-2 pb-3
                                           text-[10px] font-extrabold
                                           uppercase tracking-[0.15em]
                                           transition-all
                                           active:scale-95">

                                    Create Account

                                </button>

                            </div>

                        </div>



                        {{-- ================================== --}}
                        {{-- LOGIN                              --}}
                        {{-- ================================== --}}

                        <div
                            x-show="tab === 'login'"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-x-2"
                            x-transition:enter-end="opacity-100 translate-x-0"
                            class="p-6 sm:p-7">


                            <form
                                method="POST"
                                action="{{ route('login') }}"
                                @submit="loginLoading = true"
                                class="space-y-5">

                                @csrf


                                <div>

                                    <label
                                        for="login_email"
                                        class="mb-2 block
                                               text-[9px]
                                               font-extrabold uppercase
                                               tracking-[0.15em]
                                               text-slate-500">

                                        Email Address

                                    </label>


                                    <input
                                        id="login_email"
                                        type="email"
                                        name="email"
                                        value="{{ old('email') }}"
                                        required
                                        autocomplete="email"
                                        placeholder="you@example.com"
                                        class="w-full rounded-xl
                                               border border-slate-200
                                               bg-slate-50 px-4 py-3
                                               text-sm font-medium
                                               text-slate-800
                                               outline-none
                                               transition-all
                                               placeholder:text-slate-300
                                               focus:border-indigo-400
                                               focus:bg-white
                                               focus:ring-4
                                               focus:ring-indigo-100/60">

                                </div>



                                <div>

                                    <div
                                        class="mb-2 flex
                                               items-center
                                               justify-between">

                                        <label
                                            for="login_password"
                                            class="text-[9px]
                                                   font-extrabold uppercase
                                                   tracking-[0.15em]
                                                   text-slate-500">

                                            Password

                                        </label>


                                        @if(Route::has('password.request'))

                                            <a
                                                href="{{ route('password.request') }}"
                                                class="text-[10px]
                                                       font-bold
                                                       text-indigo-600
                                                       transition-colors
                                                       hover:text-indigo-800">

                                                Forgot password?

                                            </a>

                                        @endif

                                    </div>


                                    <input
                                        id="login_password"
                                        type="password"
                                        name="password"
                                        required
                                        autocomplete="current-password"
                                        placeholder="••••••••"
                                        class="w-full rounded-xl
                                               border border-slate-200
                                               bg-slate-50 px-4 py-3
                                               text-sm font-medium
                                               text-slate-800
                                               outline-none
                                               transition-all
                                               placeholder:text-slate-300
                                               focus:border-indigo-400
                                               focus:bg-white
                                               focus:ring-4
                                               focus:ring-indigo-100/60">

                                </div>



                                <label
                                    class="flex cursor-pointer
                                           items-center gap-2">

                                    <input
                                        type="checkbox"
                                        name="remember"
                                        class="h-4 w-4 rounded
                                               border-slate-300
                                               text-indigo-600
                                               focus:ring-indigo-500">

                                    <span
                                        class="text-[11px]
                                               font-medium
                                               text-slate-500">

                                        Keep me signed in

                                    </span>

                                </label>



                                @if(
                                    $errors->any()
                                    && !old('role')
                                )

                                    <div
                                        class="border border-rose-200
                                               bg-rose-50
                                               px-4 py-3">

                                        <p
                                            class="text-[10px]
                                                   font-bold
                                                   text-rose-700">

                                            {{ $errors->first() }}

                                        </p>

                                    </div>

                                @endif



                                <button
                                    type="submit"
                                    :disabled="loginLoading"
                                    class="group flex w-full
                                           items-center justify-center
                                           gap-2 rounded-xl
                                           bg-slate-950 px-5 py-3
                                           text-xs font-bold
                                           text-white transition-all
                                           duration-200
                                           hover:-translate-y-0.5
                                           hover:bg-indigo-600
                                           hover:shadow-lg
                                           active:translate-y-0
                                           active:scale-[0.98]
                                           disabled:cursor-not-allowed
                                           disabled:opacity-60">


                                    <svg
                                        x-cloak
                                        x-show="loginLoading"
                                        class="h-4 w-4 animate-spin"
                                        viewBox="0 0 24 24"
                                        fill="none">

                                        <circle
                                            class="opacity-25"
                                            cx="12"
                                            cy="12"
                                            r="10"
                                            stroke="currentColor"
                                            stroke-width="4">
                                        </circle>

                                        <path
                                            class="opacity-75"
                                            fill="currentColor"
                                            d="M4 12a8 8 0 018-8v4
                                               a4 4 0 00-4 4H4z">
                                        </path>

                                    </svg>


                                    <span
                                        x-text="loginLoading
                                            ? 'Signing In...'
                                            : 'Enter Workspace'">
                                    </span>


                                    <span
                                        x-show="!loginLoading"
                                        class="transition-transform
                                               group-hover:translate-x-1">
                                        →
                                    </span>

                                </button>

                            </form>

                        </div>



                        {{-- ================================== --}}
                        {{-- REGISTER                           --}}
                        {{-- ================================== --}}

                        <div
                            x-cloak
                            x-show="tab === 'register'"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-x-2"
                            x-transition:enter-end="opacity-100 translate-x-0"
                            class="p-6 sm:p-7">


                            <form
                                method="POST"
                                action="{{ route('register') }}"
                                @submit="registerLoading = true"
                                class="space-y-4">

                                @csrf


                                {{-- Names --}}
                                <div
                                    class="grid gap-4
                                           sm:grid-cols-2">

                                    <div>

                                        <label
                                            for="fname"
                                            class="mb-2 block
                                                   text-[9px]
                                                   font-extrabold uppercase
                                                   tracking-[0.15em]
                                                   text-slate-500">

                                            First Name

                                        </label>


                                        <input
                                            id="fname"
                                            type="text"
                                            name="fname"
                                            value="{{ old('fname') }}"
                                            required
                                            autocomplete="given-name"
                                            class="w-full rounded-xl
                                                   border border-slate-200
                                                   bg-slate-50
                                                   px-4 py-3 text-sm
                                                   font-medium
                                                   outline-none
                                                   transition-all
                                                   focus:border-indigo-400
                                                   focus:bg-white
                                                   focus:ring-4
                                                   focus:ring-indigo-100/60">


                                        @error('fname')

                                            <p
                                                class="mt-1 text-[10px]
                                                       font-semibold
                                                       text-rose-600">
                                                {{ $message }}
                                            </p>

                                        @enderror

                                    </div>



                                    <div>

                                        <label
                                            for="lname"
                                            class="mb-2 block
                                                   text-[9px]
                                                   font-extrabold uppercase
                                                   tracking-[0.15em]
                                                   text-slate-500">

                                            Last Name

                                        </label>


                                        <input
                                            id="lname"
                                            type="text"
                                            name="lname"
                                            value="{{ old('lname') }}"
                                            required
                                            autocomplete="family-name"
                                            class="w-full rounded-xl
                                                   border border-slate-200
                                                   bg-slate-50
                                                   px-4 py-3 text-sm
                                                   font-medium
                                                   outline-none
                                                   transition-all
                                                   focus:border-indigo-400
                                                   focus:bg-white
                                                   focus:ring-4
                                                   focus:ring-indigo-100/60">


                                        @error('lname')

                                            <p
                                                class="mt-1 text-[10px]
                                                       font-semibold
                                                       text-rose-600">
                                                {{ $message }}
                                            </p>

                                        @enderror

                                    </div>

                                </div>



                                {{-- Email --}}
                                <div>

                                    <label
                                        for="register_email"
                                        class="mb-2 block
                                               text-[9px]
                                               font-extrabold uppercase
                                               tracking-[0.15em]
                                               text-slate-500">

                                        Email Address

                                    </label>


                                    <input
                                        id="register_email"
                                        type="email"
                                        name="email"
                                        value="{{ old('email') }}"
                                        required
                                        autocomplete="email"
                                        class="w-full rounded-xl
                                               border border-slate-200
                                               bg-slate-50
                                               px-4 py-3 text-sm
                                               font-medium
                                               outline-none
                                               transition-all
                                               focus:border-indigo-400
                                               focus:bg-white
                                               focus:ring-4
                                               focus:ring-indigo-100/60">


                                    @error('email')

                                        <p
                                            class="mt-1 text-[10px]
                                                   font-semibold
                                                   text-rose-600">
                                            {{ $message }}
                                        </p>

                                    @enderror

                                </div>



                                {{-- Role --}}
                                <div>

                                    <label
                                        for="role"
                                        class="mb-2 block
                                               text-[9px]
                                               font-extrabold uppercase
                                               tracking-[0.15em]
                                               text-slate-500">

                                        Account Role

                                    </label>


                                    <select
                                        id="role"
                                        name="role"
                                        required
                                        class="w-full rounded-xl
                                               border border-slate-200
                                               bg-slate-50
                                               px-4 py-3 text-sm
                                               font-medium
                                               text-slate-700
                                               outline-none
                                               transition-all
                                               focus:border-indigo-400
                                               focus:bg-white
                                               focus:ring-4
                                               focus:ring-indigo-100/60">

                                        <option
                                            value=""
                                            disabled
                                            {{ old('role') ? '' : 'selected' }}>

                                            Select your role

                                        </option>


                                        <option
                                            value="student"
                                            {{ old('role') === 'student'
                                                ? 'selected'
                                                : '' }}>

                                            Student

                                        </option>


                                        <option
                                            value="teacher"
                                            {{ old('role') === 'teacher'
                                                ? 'selected'
                                                : '' }}>

                                            Teacher

                                        </option>

                                    </select>


                                    @error('role')

                                        <p
                                            class="mt-1 text-[10px]
                                                   font-semibold
                                                   text-rose-600">
                                            {{ $message }}
                                        </p>

                                    @enderror

                                </div>



                                {{-- Password --}}
                                <div
                                    class="grid gap-4
                                           sm:grid-cols-2">

                                    <div>

                                        <label
                                            for="register_password"
                                            class="mb-2 block
                                                   text-[9px]
                                                   font-extrabold uppercase
                                                   tracking-[0.15em]
                                                   text-slate-500">

                                            Password

                                        </label>


                                        <input
                                            id="register_password"
                                            type="password"
                                            name="password"
                                            required
                                            autocomplete="new-password"
                                            placeholder="••••••••"
                                            class="w-full rounded-xl
                                                   border border-slate-200
                                                   bg-slate-50
                                                   px-4 py-3 text-sm
                                                   font-medium
                                                   outline-none
                                                   transition-all
                                                   focus:border-indigo-400
                                                   focus:bg-white
                                                   focus:ring-4
                                                   focus:ring-indigo-100/60">

                                    </div>



                                    <div>

                                        <label
                                            for="password_confirmation"
                                            class="mb-2 block
                                                   text-[9px]
                                                   font-extrabold uppercase
                                                   tracking-[0.15em]
                                                   text-slate-500">

                                            Confirm Password

                                        </label>


                                        <input
                                            id="password_confirmation"
                                            type="password"
                                            name="password_confirmation"
                                            required
                                            autocomplete="new-password"
                                            placeholder="••••••••"
                                            class="w-full rounded-xl
                                                   border border-slate-200
                                                   bg-slate-50
                                                   px-4 py-3 text-sm
                                                   font-medium
                                                   outline-none
                                                   transition-all
                                                   focus:border-indigo-400
                                                   focus:bg-white
                                                   focus:ring-4
                                                   focus:ring-indigo-100/60">

                                    </div>

                                </div>


                                @error('password')

                                    <p
                                        class="text-[10px]
                                               font-semibold
                                               text-rose-600">
                                        {{ $message }}
                                    </p>

                                @enderror



                                <button
                                    type="submit"
                                    :disabled="registerLoading"
                                    class="group flex w-full
                                           items-center justify-center
                                           gap-2 rounded-xl
                                           bg-indigo-600
                                           px-5 py-3
                                           text-xs font-bold
                                           text-white transition-all
                                           duration-200
                                           hover:-translate-y-0.5
                                           hover:bg-indigo-500
                                           hover:shadow-lg
                                           hover:shadow-indigo-500/20
                                           active:translate-y-0
                                           active:scale-[0.98]
                                           disabled:cursor-not-allowed
                                           disabled:opacity-60">


                                    <svg
                                        x-cloak
                                        x-show="registerLoading"
                                        class="h-4 w-4 animate-spin"
                                        viewBox="0 0 24 24"
                                        fill="none">

                                        <circle
                                            class="opacity-25"
                                            cx="12"
                                            cy="12"
                                            r="10"
                                            stroke="currentColor"
                                            stroke-width="4">
                                        </circle>

                                        <path
                                            class="opacity-75"
                                            fill="currentColor"
                                            d="M4 12a8 8 0 018-8v4
                                               a4 4 0 00-4 4H4z">
                                        </path>

                                    </svg>


                                    <span
                                        x-text="registerLoading
                                            ? 'Creating Account...'
                                            : 'Create Account'">
                                    </span>


                                    <span
                                        x-show="!registerLoading"
                                        class="transition-transform
                                               group-hover:translate-x-1">
                                        →
                                    </span>

                                </button>

                            </form>

                        </div>

                    </section>

                </div>

            @endauth

        </div>

    </main>



    {{-- ====================================================== --}}
    {{-- FOOTER                                                 --}}
    {{-- ====================================================== --}}

    <footer class="border-t border-slate-200 bg-white">

        <div
            class="mx-auto flex max-w-7xl
                   flex-col gap-2 px-4 py-5
                   text-[9px] font-semibold
                   text-slate-400 sm:flex-row
                   sm:items-center
                   sm:justify-between
                   sm:px-6 lg:px-8">

            <p>
                AI-Q · AI-Driven Examination Management
                and Analytics System
            </p>


            <p>
                University of Cebu Lapu-Lapu and Mandaue
            </p>

        </div>

    </footer>

</div>

</body>

</html>