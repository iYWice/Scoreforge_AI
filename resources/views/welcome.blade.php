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

    <style>
        :root {
            --aiq-purple: #7047df;
            --aiq-deep: #4f2db5;
            --aiq-ink: #282333;
            --aiq-muted: #777382;
            --aiq-paper: #fffdf8;
            --aiq-pink: #f4a8e9;
            --aiq-yellow: #ffe16a;
            --aiq-line: #e8e4dc;
        }
        html { scroll-behavior: smooth; }
        body.aiq-page {
            background: #e9e1f3 !important;
            color: var(--aiq-ink);
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", sans-serif;
        }
        #aiq-shell { max-width: 1480px; margin: 0 auto; background: var(--aiq-paper); box-shadow: 0 24px 80px #38225618; }
        #aiq-site-header { position: relative; z-index: 5; background: var(--aiq-purple); color: #fff; border-color: #ffffff30; }
        #aiq-site-header a, #aiq-site-header p, #aiq-site-header span { color: inherit; }
        #aiq-site-header .text-slate-300, #aiq-site-header .text-slate-400 { color: #ded4ff; }
        #aiq-site-header img { height: 39px; border-radius: 10px; box-shadow: 0 3px 0 #42268c; }
        #aiq-site-header button, #aiq-site-header a[href*="dashboard"] { border-radius: 9px; box-shadow: 0 3px 0 #482b9b; }
        #aiq-main { background: radial-gradient(circle at 9% 12%, #f4ecff 0, transparent 28%), var(--aiq-paper); }
        .landing-grid { gap: clamp(28px, 5vw, 72px) !important; }
        .landing-hero { position: relative; isolation: isolate; padding: clamp(20px, 3vw, 38px) 0; }
        .landing-hero::before { content: ""; position: absolute; z-index: -1; width: 210px; height: 210px; left: -100px; top: 20%; border-radius: 50%; background: #f5a7e522; filter: blur(2px); }
        .landing-eyebrow { width: fit-content; padding: 8px 12px; border: 1px solid #ded4fb !important; border-radius: 999px; background: #f7f2ff; }
        .landing-hero h1 { max-width: 720px; color: var(--aiq-ink) !important; text-wrap: balance; }
        .landing-hero h1 span { color: var(--aiq-purple) !important; }
        .landing-hero > p { max-width: 620px !important; color: var(--aiq-muted) !important; }
        .hero-scene {
            position: relative; min-height: 210px; margin: 25px 0 28px; display: grid; place-items: center;
            perspective: 900px; isolation: isolate; outline: none; --tilt-x: 0deg; --tilt-y: 0deg;
        }
        .hero-scene::before { content: ""; position: absolute; width: min(78%, 460px); height: 65%; bottom: 5px; left: 11%; border-radius: 50%; background: #6e48d52a; filter: blur(28px); z-index: -1; }
        .scene-board {
            position: relative; width: min(76%, 410px); min-height: 174px; padding: 20px 22px; border: 2px solid #30243e; border-radius: 17px;
            background: linear-gradient(135deg, #ffdf65 0%, #ffdc63 54%, #ffd557 100%);
            box-shadow: 8px 9px 0 #30243e, 0 22px 38px #33234420;
            transform: rotateX(var(--tilt-x)) rotateY(var(--tilt-y)) rotate(-2deg); transform-style: preserve-3d;
            transition: transform .18s ease-out, box-shadow .2s ease; animation: board-float 5s ease-in-out infinite;
        }
        .scene-board::before { content: ""; position: absolute; inset: 10px; border: 1px solid #42334760; border-radius: 11px; pointer-events: none; }
        .scene-board::after { content: ""; position: absolute; width: 80px; height: 80px; right: 24px; top: 39px; border-radius: 50%; background: radial-gradient(circle at 32% 27%, #fff7 0 5%, transparent 7%), radial-gradient(circle at 35% 30%, #f9a5d2, #8d54e9 56%, #35bdae 74%); box-shadow: inset -10px -12px 18px #31224c66, 5px 8px 0 #30243e; transform: translateZ(25px); }
        .scene-label { position: relative; z-index: 1; display: inline-flex; padding: 5px 8px; border: 1px solid #33283d; border-radius: 5px; background: #fffdf4; color: #5232b4; font-size: 9px; font-weight: 900; letter-spacing: .14em; transform: translateZ(24px); }
        .scene-title { position: relative; z-index: 1; max-width: 230px; margin: 14px 0 5px; color: #292237; font-size: clamp(19px, 2.3vw, 27px); line-height: 1.03; font-weight: 950; letter-spacing: -.06em; transform: translateZ(34px); }
        .scene-caption { position: relative; z-index: 1; margin: 0; color: #514555; font-size: 10px; transform: translateZ(24px); }
        .scene-sticker { position: absolute; z-index: 3; display: grid; place-items: center; width: 47px; height: 47px; border: 1.5px solid #30243e; border-radius: 50%; background: #f38ddb; box-shadow: 3px 4px 0 #30243e; color: #30243e; font-size: 21px; animation: sticker-float 4s ease-in-out infinite; }
        .scene-sticker.one { top: 13px; left: 12%; transform: translateZ(50px) rotate(-9deg); }
        .scene-sticker.two { bottom: 14px; right: 10%; background: #ff9b45; animation-delay: -1.2s; }
        .scene-note { position: absolute; z-index: 4; padding: 8px 11px; border: 1px solid #30243e; border-radius: 8px; background: #fffdf8; box-shadow: 3px 4px 0 #30243e; color: #30243e; font-size: 9px; font-weight: 800; }
        .scene-note.left { left: 2%; top: 31%; transform: rotate(-5deg) translateZ(55px); }
        .scene-note.right { right: 1%; top: 17%; background: #f4a8e9; transform: rotate(4deg) translateZ(55px); }
        .scene-motion { position: absolute; right: 8%; bottom: 0; z-index: 5; border: 1px solid #33253f; border-radius: 999px; padding: 7px 11px; background: #fff; color: var(--aiq-deep); font-size: 9px; font-weight: 900; cursor: pointer; box-shadow: 2px 3px 0 #33253f; transition: transform .2s, background .2s; }
        .scene-motion:hover { transform: translateY(-2px); background: var(--aiq-pink); }
        .hero-scene.paused .scene-board, .hero-scene.paused .scene-sticker { animation-play-state: paused; }
        .feature-grid { gap: 9px !important; border: 0 !important; padding-top: 0 !important; }
        .feature-tile { min-height: 118px; padding: 15px; border: 1px solid #3b3043; border-radius: 10px; background: #fff; box-shadow: 0 4px 0 #3b3043; transition: transform .2s, box-shadow .2s; }
        .feature-tile:hover { transform: translateY(-5px) rotate(-.5deg); box-shadow: 0 9px 0 #3b3043; }
        .feature-tile:nth-child(2) { background: #f4a8e9; }
        .feature-tile:nth-child(3) { background: #dff1dc; }
        .feature-tile p:last-child { color: #514b5a !important; }
        .auth-card { overflow: hidden; border: 1.5px solid #33253f !important; border-radius: 14px; background: #fffdf8 !important; box-shadow: 7px 8px 0 #33253f, 0 20px 55px #3723481c !important; transform-style: preserve-3d; transition: transform .25s, box-shadow .25s; }
        .auth-card:hover { transform: translateY(-3px) rotateY(-1deg); box-shadow: 9px 11px 0 #33253f, 0 25px 60px #37234822 !important; }
        .auth-card-head { padding-top: 24px !important; background: linear-gradient(140deg, #fffdf8, #f5edff); }
        .auth-card h2 { color: var(--aiq-ink) !important; }
        .auth-card [x-show] button { transition: color .2s, border-color .2s, transform .2s; }
        .auth-card input:not([type="checkbox"]), .auth-card select { border-radius: 9px !important; background: #fff !important; }
        .auth-card input:focus, .auth-card select:focus { box-shadow: 0 0 0 4px #7047df1c !important; }
        .auth-card form button[type="submit"] { border: 1px solid #30243e; border-radius: 9px !important; background: var(--aiq-purple) !important; box-shadow: 0 4px 0 #392476; }
        .auth-card form button[type="submit"]:hover { background: var(--aiq-deep) !important; box-shadow: 0 6px 0 #392476; }
        #aiq-main > div > section { border-radius: 14px; }
        #aiq-footer { background: #faf7f0; border-color: var(--aiq-line); }
        @keyframes board-float { 0%,100% { translate: 0 0; } 50% { translate: 0 -7px; } }
        @keyframes sticker-float { 0%,100% { margin-top: 0; } 50% { margin-top: -8px; } }
        @media (max-width: 900px) {
            .landing-grid { grid-template-columns: minmax(0,1fr) !important; }
            .landing-hero { padding-bottom: 6px; }
            #auth-workspace { width: min(100%, 560px); margin: 0 auto; }
        }
        @media (max-width: 560px) {
            #aiq-site-header > div { height: 60px; }
            .hero-scene { min-height: 190px; margin: 20px -4px 24px; }
            .scene-board { width: 75%; min-height: 158px; padding: 17px; }
            .scene-board::after { width: 58px; height: 58px; right: 18px; top: 42px; }
            .scene-title { max-width: 170px; font-size: 19px; }
            .scene-note { font-size: 8px; padding: 6px 8px; }
            .scene-note.left { left: 0; }
            .scene-note.right { right: 0; }
            .scene-sticker { width: 38px; height: 38px; font-size: 17px; }
            .scene-motion { right: 1%; }
            .feature-grid { grid-template-columns: 1fr !important; }
            .feature-tile { min-height: 0; }
            .auth-card { box-shadow: 5px 6px 0 #33253f !important; }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { scroll-behavior: auto !important; animation-duration: .01ms !important; animation-iteration-count: 1 !important; transition-duration: .01ms !important; }
        }
    </style>


    <link
        rel="preconnect"
        href="https://fonts.bunny.net">

    <link
        href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800"
        rel="stylesheet">


    {-- Render this content conditionally based on the current view state. --}
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
    class="aiq-page min-h-full bg-slate-50
           font-sans text-slate-800 antialiased">


    <div
        x-data="{
        tab: '{{ $errors->any() && old('role') ? 'register' : 'login' }}',
        registerRole: '{{ old('role', '') }}',
        loginLoading: false,
        registerLoading: false,
        mobileAuth: false
    }"
        id="aiq-shell"
        class="flex min-h-screen flex-col">


        {{-- ====================================================== --}}
        {{-- TOP NAVIGATION                                         --}}
        {{-- ====================================================== --}}

        {-- Render this navigation or page landmark. --}
        <header
            id="aiq-site-header"
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

                    <img
                        src="{{ asset('images/Aiq_logo.jpg') }}"
                        alt="AI-Q"
                        class="h-10 w-auto">


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

                    {-- Apply the applicable access or presence condition. --}
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

                    {-- Render this content conditionally based on the current view state. --}
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

        {-- Render this meaningful page or content section. --}
        <main id="aiq-main" class="flex flex-1 items-center">

            <div
                class="mx-auto w-full max-w-7xl
                   px-4 py-10 sm:px-6 sm:py-14
                   lg:px-8 lg:py-16">


                {-- Apply the applicable access or presence condition. --}
                @auth

                {{-- ========================================== --}}
                {{-- AUTHENTICATED WELCOME                      --}}
                {{-- ========================================== --}}

                {-- Render this meaningful page or content section. --}
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


                        {-- Collect and submit the data for this operation. --}
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


                {-- Render this content conditionally based on the current view state. --}
                @else

                {{-- ========================================== --}}
                {{-- GUEST WORKSPACE                            --}}
                {{-- ========================================== --}}

                <div
                    class="landing-grid grid items-center gap-12
                           lg:grid-cols-[minmax(0,1fr)_440px]
                           xl:gap-20">


                    {{-- ====================================== --}}
                    {{-- LEFT: AI-Q IDENTITY                    --}}
                    {{-- ====================================== --}}

                    {-- Render this meaningful page or content section. --}
                    <section class="landing-hero">


                        <div
                            class="landing-eyebrow inline-flex items-center
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
                            and analytics platform designed by the students of
                            the University of Cebu Lapu-Lapu
                            and Mandaue. Create and take
                            examinations, process scores
                            automatically, and monitor academic
                            performance from one workspace.

                        </p>



                        <div class="hero-scene" id="hero-scene" data-tilt tabindex="0" aria-label="Interactive 3D AI-Q learning card">
                            <span class="scene-sticker one" aria-hidden="true">✦</span>
                            <span class="scene-sticker two" aria-hidden="true">✎</span>
                            <span class="scene-note left">LEARN · PRACTICE</span>
                            <span class="scene-note right">AI-Q INSIGHTS</span>
                            <div class="scene-board">
                                <span class="scene-label">YOUR LEARNING SPACE</span>
                                <h2 class="scene-title">Think clearly.<br>Grow confidently.</h2>
                                <p class="scene-caption">One place for exams, results, and progress.</p>
                            </div>
                            <button type="button" class="scene-motion" id="scene-motion" aria-pressed="false">Ⅱ &nbsp; Pause motion</button>
                        </div>


                        {{-- Feature signals --}}
                        <div
                            class="mt-10 grid gap-x-8
                                   gap-y-6 border-y
                                   border-slate-200 py-7
                                   sm:grid-cols-3 feature-grid">


                            <div class="feature-tile">

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
                                class="feature-tile sm:border-l
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
                                class="feature-tile sm:border-l
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

                    {-- Render this meaningful page or content section. --}
                    <section
                        id="auth-workspace"
                        class="auth-card border border-slate-200
                               bg-white shadow-xl
                               shadow-slate-900/[0.04]">


                        {{-- Header --}}
                        <div
                            class="auth-card-head border-b border-slate-200
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


                            {-- Collect and submit the data for this operation. --}
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


                                        {-- Render this content conditionally based on the current view state. --}
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



                                {-- Render this content conditionally based on the current view state. --}
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


                            {-- Collect and submit the data for this operation. --}
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
                                        x-model="registerRole"
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


                                {{-- Account ID --}}
                                <div>
                                    <label
                                        for="account_id"
                                        class="mb-2 block
                                               text-[9px]
                                               font-extrabold uppercase
                                               tracking-[0.15em]
                                               text-slate-500">
                                        Account ID
                                    </label>

                                    <div class="flex gap-2">
                                        <input
                                            id="account_id"
                                            type="number"
                                            name="account_id"
                                            value="{{ old('account_id') }}"
                                            autocomplete="off"
                                            inputmode="numeric"
                                            pattern="[0-9]*"
                                            maxlength="50"
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                            placeholder="Enter your account ID"
                                            class="min-w-0 flex-1 rounded-xl
                                                   border border-slate-200
                                                   bg-slate-50
                                                   px-3 py-3 text-sm
                                                   font-medium
                                                   outline-none
                                                   transition-all
                                                   focus:border-indigo-400
                                                   focus:bg-white
                                                   focus:ring-4
                                                   focus:ring-indigo-100/60">

                                        <button
                                            type="button"
                                            id="check-account-id"
                                            class="shrink-0 rounded-xl border border-indigo-200 bg-indigo-50 px-3 text-[10px] font-extrabold text-indigo-700 transition hover:bg-indigo-100">
                                            Check
                                        </button>
                                    </div>

                                    <p id="account_id_status" class="mt-1 min-h-4 text-[10px] font-semibold"></p>
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

        {-- Render this navigation or page landmark. --}
        <footer id="aiq-footer" class="border-t border-slate-200 bg-white">

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

    <script>
        const heroScene = document.getElementById('hero-scene');
        const sceneMotion = document.getElementById('scene-motion');
        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        if (heroScene && sceneMotion) {
            if (reduceMotion) {
                heroScene.classList.add('paused');
                sceneMotion.textContent = '↻  Motion reduced';
                sceneMotion.disabled = true;
            } else {
                const setTilt = (x, y) => {
                    const bounds = heroScene.getBoundingClientRect();
                    const px = (x - bounds.left) / bounds.width;
                    const py = (y - bounds.top) / bounds.height;
                    heroScene.style.setProperty('--tilt-x', `${(0.5 - py) * 10}deg`);
                    heroScene.style.setProperty('--tilt-y', `${(px - 0.5) * 12}deg`);
                };
                heroScene.addEventListener('pointermove', event => setTilt(event.clientX, event.clientY));
                heroScene.addEventListener('pointerleave', () => {
                    heroScene.style.setProperty('--tilt-x', '0deg');
                    heroScene.style.setProperty('--tilt-y', '0deg');
                });
                heroScene.addEventListener('keydown', event => {
                    if (event.key === 'Enter' || event.key === ' ') {
                        event.preventDefault();
                        heroScene.classList.toggle('paused');
                        sceneMotion.setAttribute('aria-pressed', heroScene.classList.contains('paused'));
                        sceneMotion.textContent = heroScene.classList.contains('paused') ? '▶  Resume motion' : 'Ⅱ  Pause motion';
                    }
                });
                sceneMotion.addEventListener('click', () => {
                    heroScene.classList.toggle('paused');
                    const paused = heroScene.classList.contains('paused');
                    sceneMotion.setAttribute('aria-pressed', paused);
                    sceneMotion.textContent = paused ? '▶  Resume motion' : 'Ⅱ  Pause motion';
                });
            }
        }

        const accountIdCheck = document.getElementById('check-account-id');
        const accountIdInput = document.getElementById('account_id');
        const accountIdStatus = document.getElementById('account_id_status');

        accountIdCheck?.addEventListener('click', async () => {
            const value = accountIdInput?.value.trim() || '';
            const role = document.getElementById('role')?.value || '';

            if (!value) {
                accountIdStatus.textContent = 'Enter an Account ID first.';
                accountIdStatus.className = 'mt-1 min-h-4 text-[10px] font-semibold text-amber-600';
                accountIdInput.focus();
                return;
            }

            if (!['student', 'teacher'].includes(role)) {
                accountIdStatus.textContent = 'Select your account role first.';
                accountIdStatus.className = 'mt-1 min-h-4 text-[10px] font-semibold text-amber-600';
                document.getElementById('role')?.focus();
                return;
            }

            accountIdStatus.textContent = 'Checking...';
            accountIdStatus.className = 'mt-1 min-h-4 text-[10px] font-semibold text-slate-500';
            accountIdCheck.disabled = true;

            try {
                const response = await fetch('{{ route('register.check.account-id')}}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ account_id: value, role: role })
                });

                const data = await response.json();
                accountIdStatus.textContent = data.message;
                accountIdStatus.className = data.available
                    ? 'mt-1 min-h-4 text-[10px] font-semibold text-emerald-600'
                    : 'mt-1 min-h-4 text-[10px] font-semibold text-rose-600';

                accountIdInput.setAttribute('aria-invalid', data.available ? 'false' : 'true');
            } catch (error) {
                accountIdStatus.textContent = 'Could not check the ID. Please try again.';
                accountIdStatus.className = 'mt-1 min-h-4 text-[10px] font-semibold text-rose-600';
            } finally {
                accountIdCheck.disabled = false;
            }
        });

        accountIdInput?.addEventListener('input', () => {
            accountIdStatus.textContent = '';
            accountIdInput.removeAttribute('aria-invalid');
        });
    </script>
</body>

</html>