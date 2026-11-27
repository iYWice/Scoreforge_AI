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

        /* AI-Q 16 inspired command shell */
        body.aiq-app-shell {
            background: #e9e1f3 !important;
            color: #292437;
        }
        #aiq-app-root {
            min-height: 100vh;
            background: radial-gradient(circle at 5% 7%, #f4ecff 0, transparent 24%), #fffdf8;
            box-shadow: 0 18px 60px #38225616;
        }
        #aiq-global-header {
            border-color: #ffffff35 !important;
            background: #7047df !important;
            color: #fff;
            box-shadow: 0 4px 0 #4d2cae;
        }
        #aiq-global-header a, #aiq-global-header p,
        #aiq-global-header nav, #aiq-global-header button { color: inherit; }
        #aiq-global-header img {
            border-radius: 10px;
            box-shadow: 0 3px 0 #43258e;
        }
        #aiq-global-header .text-slate-950 { color: #fff !important; }
        #aiq-global-header .text-slate-500,
        #aiq-global-header .text-slate-400 { color: #e4dbff !important; }
        #aiq-global-header .bg-indigo-50 {
            border: 1px solid #ffffff55;
            background: #ffffff20 !important;
            color: #fff !important;
        }
        #aiq-global-header nav a {
            transform: translateY(0);
            transition: color .18s, transform .18s, background .18s;
        }
        #aiq-global-header nav a:hover { color: #ffe16a !important; transform: translateY(-2px); }
        #aiq-global-header nav a.text-slate-950 { font-weight: 850; }
        #aiq-global-header nav a span { background: #ffe16a !important; }
        #aiq-create-exam {
            border: 1px solid #30243e;
            border-radius: 9px;
            background: #ffe16a !important;
            color: #30243e !important;
            box-shadow: 0 4px 0 #30243e;
        }
        #aiq-create-exam:hover { background: #fff0a6 !important; box-shadow: 0 6px 0 #30243e; }
        #aiq-page-actions a, #aiq-page-actions button {
            border: 1px solid #30243e;
            border-radius: 9px;
            background: #ffe16a !important;
            color: #30243e !important;
            box-shadow: 0 4px 0 #30243e;
        }
        #aiq-page-actions a:hover, #aiq-page-actions button:hover {
            background: #fff0a6 !important;
            box-shadow: 0 6px 0 #30243e;
        }
        #aiq-profile-trigger {
            border-radius: 999px;
            background: #ffffff14;
            transition: background .2s, transform .2s;
        }
        #aiq-profile-trigger:hover { background: #ffffff27 !important; transform: translateY(-1px); }
        #aiq-profile-trigger > div {
            border: 1px solid #ffffff80;
            background: #f4a8e9 !important;
            color: #382443 !important;
            box-shadow: 0 2px 0 #382443;
        }
        #aiq-profile-dropdown {
            border: 1.5px solid #33253f !important;
            border-radius: 14px !important;
            background: #fffdf8 !important;
            color: #292437;
            box-shadow: 5px 6px 0 #33253f, 0 20px 45px #32223f26 !important;
            transform-origin: top right;
        }
        #aiq-profile-dropdown p { color: #292437 !important; }
        #aiq-profile-dropdown a { color: #4f2db5 !important; }
        #aiq-profile-dropdown button { color: #51475d !important; }
        #aiq-profile-dropdown .text-slate-400 { color: #777382 !important; }
        #aiq-profile-dropdown a:hover, #aiq-profile-dropdown button:hover { background: #f2eaff !important; }
        #aiq-mobile-toggle { color: #fff !important; }
        #aiq-mobile-toggle:hover { background: #ffffff20 !important; }
        #aiq-mobile-navigation {
            border-color: #ffffff35 !important;
            background: #5c39c5;
            padding: 10px 0 14px;
        }
        #aiq-mobile-navigation a {
            color: #fff !important;
            transition: transform .16s, background .16s;
        }
        #aiq-mobile-navigation a:hover { background: #ffffff1c !important; transform: translateX(3px); }
        #aiq-main { min-height: calc(100vh - 72px); }
        #aiq-context-header {
            position: relative;
            overflow: hidden;
            border-bottom: 1px solid #e8e4dc !important;
            background: linear-gradient(105deg, #fffdf8 0%, #fff 68%, #f4edff 100%) !important;
        }
        #aiq-context-header::after {
            content: "";
            position: absolute;
            width: 125px;
            height: 125px;
            right: 7%;
            top: -68px;
            border: 1px solid #7047df20;
            border-radius: 50%;
            box-shadow: 0 0 0 16px #7047df08, 0 0 0 35px #7047df05;
            pointer-events: none;
        }
        #aiq-context-header h1 { color: #292437 !important; letter-spacing: -.05em; }
        #aiq-page-content { position: relative; }
        #aiq-page-content input:not([type="checkbox"]):not([type="radio"]),
        #aiq-page-content select, #aiq-page-content textarea {
            border-radius: 9px;
            transition: border-color .18s, box-shadow .18s, background .18s;
        }
        #aiq-page-content input:focus, #aiq-page-content select:focus,
        #aiq-page-content textarea:focus {
            border-color: #9c80eb;
            box-shadow: 0 0 0 4px #7047df1c;
            outline: none;
        }
        #aiq-page-content button:not([data-plain]) {
            transition: transform .18s ease, box-shadow .18s ease, background .18s ease;
        }
        #aiq-page-content button:not([data-plain]):active { transform: scale(.97); }
        #aiq-success-toast, #aiq-error-toast {
            border: 1.5px solid #35283f !important;
            border-radius: 12px !important;
            box-shadow: 4px 5px 0 #35283f, 0 15px 30px #35283f20 !important;
        }
        @media (max-width: 640px) {
            #aiq-global-header .flex.h-\[72px\] { height: 64px; }
            #aiq-context-header::after { right: -20px; opacity: .6; }
        }

        /* Shared raised controls across authenticated pages */
        #aiq-page-content input:not([type="checkbox"]):not([type="radio"]):not([type="file"]),
        #aiq-page-content select,
        #aiq-page-content textarea {
            border: 1.5px solid #d8cee4 !important;
            border-radius: 10px !important;
            background-color: #fffdf8 !important;
            color: #292437 !important;
            box-shadow: inset 0 1px 2px #30243e0b !important;
            transition: border-color .18s ease, box-shadow .18s ease, background .18s ease !important;
        }
        #aiq-page-content input::placeholder,
        #aiq-page-content textarea::placeholder { color: #9a91a2 !important; }
        #aiq-page-content input:focus,
        #aiq-page-content select:focus,
        #aiq-page-content textarea:focus {
            border-color: #8f70e2 !important;
            background-color: #fff !important;
            box-shadow: 0 0 0 4px #7047df20, inset 0 1px 2px #30243e08 !important;
            outline: none !important;
        }
        #aiq-page-content input[readonly],
        #aiq-page-content input:disabled,
        #aiq-page-content select:disabled,
        #aiq-page-content textarea:disabled {
            background-color: #f3eff7 !important;
            color: #777080 !important;
        }
        #aiq-page-content input[type="checkbox"],
        #aiq-page-content input[type="radio"] { accent-color: #7047df !important; }
        #aiq-page-content input[type="file"]::file-selector-button {
            margin-right: 10px; padding: 7px 11px; border: 1px solid #382d43;
            border-radius: 7px; background: #f2eaff; color: #4f32a9;
            font: inherit; font-weight: 700; cursor: pointer;
        }
        #aiq-page-content button:not([data-plain]) {
            border: 1px solid #382d43 !important;
            border-radius: 9px !important;
            box-shadow: 0 3px 0 #382d43 !important;
            transition: transform .16s ease, box-shadow .16s ease, filter .16s ease !important;
        }
        #aiq-page-content button[class*="bg-indigo"],
        #aiq-page-content button[class*="bg-slate-950"],
        #aiq-page-content a[class*="bg-indigo"],
        #aiq-page-content a[class*="bg-slate-950"] {
            border-color: #30243e !important;
            box-shadow: 0 4px 0 #30243e !important;
        }
        #aiq-page-content button[class*="bg-rose"],
        #aiq-page-content button[class*="bg-red"],
        #aiq-page-content a[class*="bg-rose"],
        #aiq-page-content a[class*="bg-red"] {
            border-color: #853455 !important;
            box-shadow: 0 3px 0 #853455 !important;
        }
        #aiq-page-content button[class*="bg-emerald"],
        #aiq-page-content button[class*="bg-green"] {
            border-color: #285a43 !important;
            box-shadow: 0 3px 0 #285a43 !important;
        }
        #aiq-page-content button:hover:not(:disabled):not([data-plain]) {
            transform: translateY(-2px) !important;
            filter: brightness(1.03);
            box-shadow: 0 5px 0 #382d43 !important;
        }
        #aiq-page-content button:active:not(:disabled):not([data-plain]) {
            transform: translateY(1px) scale(.98) !important;
            box-shadow: 0 1px 0 #382d43 !important;
        }
        #aiq-page-content button:disabled {
            cursor: not-allowed !important;
            opacity: .58;
            box-shadow: 0 2px 0 #382d4380 !important;
        }
        #aiq-page-content button:focus-visible,
        #aiq-page-content a[class*="bg-"]:focus-visible {
            outline: 3px solid #7047df66 !important;
            outline-offset: 3px;
        }

        /* Shared AIQ 16 surfaces, tables, chips, alerts and overlays */
        #aiq-page-content {
            background: radial-gradient(circle at 2% 2%, #f4edff 0, transparent 18%), transparent;
            border-radius: 16px;
        }
        #aiq-page-content :is(div,article,section,aside)[class~="bg-white"][class~="border"] {
            border-color: #ded6e7 !important;
            border-radius: 12px !important;
            background-color: #fffdf8 !important;
            box-shadow: 0 4px 0 #382d4320;
        }
        #aiq-page-content :is(div,article,section,aside)[class~="bg-white"][class~="border"] > :is(div,header):first-child[class*="border-b"] {
            border-color: #e5dce9 !important;
            background: linear-gradient(110deg, #f7f1ff, #fffdf8) !important;
        }
        #aiq-page-content [class~="bg-slate-950"] {
            border-color: #30243e !important;
            border-radius: 13px !important;
            background: linear-gradient(145deg, #7047df, #5230b5) !important;
            box-shadow: 5px 6px 0 #30243e !important;
        }
        #aiq-page-content [class~="bg-slate-950"] :is(h1,h2,h3,h4) { color: #fff !important; }
        #aiq-page-content [class~="bg-slate-950"] .text-slate-400,
        #aiq-page-content [class~="bg-slate-950"] .text-slate-500 { color: #e4dbff !important; }
        #aiq-page-content [class~="bg-slate-950"] .text-indigo-400,
        #aiq-page-content [class~="bg-slate-950"] .text-indigo-300 { color: #ffe16a !important; }
        #aiq-page-content :is(div,article,section)[class*="bg-emerald-50"][class~="border"],
        #aiq-page-content :is(div,article,section)[class*="bg-emerald-100"][class~="border"] {
            border-color: #8ab69a !important; border-radius: 11px !important; box-shadow: 0 3px 0 #4d855e35;
        }
        #aiq-page-content :is(div,article,section)[class*="bg-rose-50"][class~="border"],
        #aiq-page-content :is(div,article,section)[class*="bg-red-50"][class~="border"],
        #aiq-page-content :is(div,article,section)[class*="bg-rose-100"][class~="border"] {
            border-color: #d99ab6 !important; border-radius: 11px !important; box-shadow: 0 3px 0 #a84f7430;
        }
        #aiq-page-content :is(div,article,section)[class*="bg-amber-50"][class~="border"],
        #aiq-page-content :is(div,article,section)[class*="bg-yellow-50"][class~="border"] {
            border-color: #d8bd61 !important; border-radius: 11px !important; box-shadow: 0 3px 0 #a88b2d30;
        }
        #aiq-page-content :is(div,article,section)[class*="bg-indigo-50"][class~="border"] {
            border-color: #c5b5ec !important; border-radius: 11px !important; box-shadow: 0 3px 0 #7047df22;
        }
        #aiq-page-content table { border-collapse: separate; border-spacing: 0; }
        #aiq-page-content table thead { background: #f2eaff !important; color: #5132a8 !important; }
        #aiq-page-content table thead tr { border-color: #dcd1ed !important; }
        #aiq-page-content table th { color: #5132a8 !important; font-weight: 800; }
        #aiq-page-content table td { border-color: #ece5ef !important; }
        #aiq-page-content table tbody tr { transition: background-color .16s ease; }
        #aiq-page-content table tbody tr:hover { background: #fff8df !important; }
        #aiq-page-content span[class~="rounded-full"][class*="bg-"] {
            border: 1px solid #382d4325;
            box-shadow: 0 2px 0 #382d4318;
        }
        #aiq-page-content [class*="divide-y"] > div:hover,
        #aiq-page-content [class*="divide-y"] > a:hover { background-color: #fff8df !important; }
        #aiq-page-content [class*="border-b"][class*="bg-slate-50"],
        #aiq-page-content thead tr[class*="bg-slate-50"] { background: #f3edfb !important; }
        #aiq-page-content [role="dialog"],
        #aiq-page-content .fixed[class*="bg-white"] {
            border-color: #382d43 !important;
            border-radius: 14px !important;
            box-shadow: 6px 7px 0 #382d43, 0 22px 55px #30243e2a !important;
        }
        #aiq-page-content a[class*="rounded-xl"]:focus-visible,
        #aiq-page-content a[class*="rounded-2xl"]:focus-visible {
            outline: 3px solid #7047df66 !important;
            outline-offset: 3px;
        }

        /* Reusable AI-Q component classes */
        #aiq-page-content .aiq-panel {
            border: 1.5px solid #382d43 !important;
            border-radius: 13px !important;
            background: #fffdf8 !important;
            box-shadow: 0 4px 0 #382d43 !important;
        }
        #aiq-page-content .aiq-panel-interactive {
            transition: transform .18s ease, box-shadow .18s ease !important;
            transform-style: preserve-3d;
        }
        #aiq-page-content .aiq-panel-interactive:hover {
            transform: perspective(900px) rotateX(var(--tilt-x, 0deg)) rotateY(var(--tilt-y, 0deg)) translateY(-3px) !important;
            box-shadow: 0 8px 0 #382d43, 0 16px 30px #382d4318 !important;
        }
        #aiq-page-content .aiq-action-bar {
            border: 1.5px solid #382d43 !important;
            border-radius: 12px !important;
            background: #f4a8e9 !important;
            color: #382d43 !important;
            box-shadow: 0 4px 0 #382d43 !important;
        }
        #aiq-page-content .aiq-button {
            display: inline-flex;
            min-height: 38px;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 8px 14px;
            border: 1px solid #382d43 !important;
            border-radius: 9px !important;
            background: #fffdf8;
            color: #44364f;
            font-size: 12px;
            font-weight: 800;
            line-height: 1.25;
            box-shadow: 0 3px 0 #382d43 !important;
            transition: transform .16s ease, box-shadow .16s ease, background .16s ease !important;
        }
        #aiq-page-content .aiq-button:hover:not(:disabled) {
            transform: translateY(-2px) !important;
            box-shadow: 0 5px 0 #382d43 !important;
        }
        #aiq-page-content .aiq-button:active:not(:disabled) {
            transform: translateY(1px) scale(.98) !important;
            box-shadow: 0 1px 0 #382d43 !important;
        }
        #aiq-page-content .aiq-button-primary { background: #7047df !important; color: #fff !important; }
        #aiq-page-content .aiq-button-primary:hover:not(:disabled) { background: #5936c0 !important; }
        #aiq-page-content .aiq-button-secondary { background: #fffdf8 !important; color: #5438a2 !important; }
        #aiq-page-content .aiq-button-success { background: #dff0df !important; color: #315b39 !important; }
        #aiq-page-content .aiq-button-danger { background: #fce6f3 !important; color: #8f315f !important; }
        #aiq-page-content .aiq-button:disabled { opacity: .58; cursor: not-allowed; }
        #aiq-page-content .aiq-field {
            width: 100%;
            border: 1.5px solid #d8cee4 !important;
            border-radius: 9px !important;
            background: #fffdf8 !important;
            color: #292437 !important;
            padding: 10px 12px;
            box-shadow: inset 0 1px 2px #30243e0b !important;
            transition: border-color .18s, box-shadow .18s, background .18s !important;
        }
        #aiq-page-content .aiq-field:focus {
            border-color: #8f70e2 !important;
            background: #fff !important;
            box-shadow: 0 0 0 4px #7047df20 !important;
            outline: none !important;
        }
        #aiq-page-content .aiq-check { accent-color: #7047df !important; }
        #aiq-page-content .aiq-badge {
            display: inline-flex;
            align-items: center;
            border: 1px solid #382d4330;
            border-radius: 999px;
            background: #f0eaff !important;
            color: #5937bd !important;
            padding: 4px 9px;
            font-size: 10px;
            font-weight: 800;
        }
        #aiq-page-content .aiq-option {
            border: 1px solid #e3dce8 !important;
            border-radius: 8px !important;
            background: #fff !important;
            color: #51475d !important;
            padding: 9px 12px;
            transition: transform .16s, background .16s, box-shadow .16s;
        }
        #aiq-page-content .aiq-option:hover { transform: translateX(3px); background: #fff8df !important; box-shadow: 0 2px 0 #382d4320; }
        #aiq-page-content .aiq-dropzone {
            display: flex;
            min-height: 150px;
            align-items: center;
            justify-content: center;
            border: 2px dashed #9879e8 !important;
            border-radius: 11px !important;
            background: linear-gradient(135deg, #f5efff, #fff9e2) !important;
            transition: transform .18s, border-color .18s, box-shadow .18s;
        }
        #aiq-page-content .aiq-dropzone:hover { transform: translateY(-2px); border-color: #7047df !important; box-shadow: 0 4px 0 #7047df30; }
        #aiq-page-content .aiq-alert {
            border: 1.5px solid #382d43 !important;
            border-radius: 10px !important;
            padding: 12px 16px;
            box-shadow: 0 3px 0 #382d43 !important;
        }
        #aiq-page-content .aiq-alert-success { background: #e7f3e5 !important; color: #315b39 !important; }
        #aiq-page-content .aiq-alert-info { background: #f2eaff !important; color: #4d328d !important; }
        #aiq-page-content .aiq-alert-error { background: #fce6f3 !important; color: #8f315f !important; }
        @media (prefers-reduced-motion: reduce) {
            #aiq-app-root *, #aiq-app-root *::before, #aiq-app-root *::after {
                scroll-behavior: auto !important;
                animation-duration: .01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: .01ms !important;
            }
        }
    </style>
</head>

<body class="aiq-app-shell min-h-full bg-[#f7f8fb] text-slate-900 antialiased">

    @auth
    @if(\App\Models\SystemSetting::getValue('maintenance_notice', '0') === '1')
    <div class="border-b border-amber-200 bg-amber-50 px-5 py-3 text-center text-sm font-medium text-amber-900">
        AI-Q is currently undergoing maintenance. Some features may be temporarily unavailable.
    </div>
    @endif

    <div
        x-data="{
        mobileMenu: false,
        profileMenu: false
    }"
        id="aiq-app-root"
        class="min-h-screen">

        {{-- ========================================================= --}}
        {{-- GLOBAL COMMAND BAR                                        --}}
        {{-- ========================================================= --}}

        <header
            id="aiq-global-header"
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

                            <img
                        src="{{ asset('images/Aiq_logo.jpg') }}"
                        alt="AI-Q"
                        class="h-10 w-auto">

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
                        id="aiq-desktop-nav"
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
                            href="{{ route('teacher.classes.index') }}"
                            class="relative flex h-full items-center
           px-4 text-sm font-semibold
           transition-all duration-200
           hover:text-indigo-600
           active:scale-95
           {{ request()->routeIs('teacher.classes.*')
               ? 'text-slate-950'
               : 'text-slate-500' }}">
                            Classes

                            @if(request()->routeIs('teacher.classes.*'))
                            <span
                                class="absolute inset-x-4 bottom-0
                   h-[3px] rounded-t-full
                   bg-indigo-600">
                            </span>
                            @endif
                        </a>

{{-- Subjects --}}
<a
    href="{{ route('teacher.subjects.index') }}"
    class="relative flex h-full items-center px-4 text-sm font-semibold transition-all duration-200 hover:text-indigo-600
        {{ request()->routeIs('teacher.subjects.*')
            ? 'text-slate-950'
            : 'text-slate-500' }}">
    Subjects

    @if(request()->routeIs('teacher.subjects.*'))
        <span class="absolute inset-x-4 bottom-0 h-[3px] rounded-t-full bg-indigo-600"></span>
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
                            </span> 

                        </button>-->

                        @elseif(auth()->user()->role === 'student')

                        {{-- Overview --}}
                        <a
                            href="{{ url('/student/dashboard') }}"
                            class="relative flex h-full items-center
           px-4 text-sm font-semibold
           transition-all duration-200
           hover:text-indigo-600
           active:scale-95
           {{ request()->is('student/dashboard')
               ? 'text-slate-950'
               : 'text-slate-500' }}">
                            Overview

                            @if(request()->is('student/dashboard'))
                            <span
                                class="absolute inset-x-4 bottom-0
                   h-[3px] rounded-t-full
                   bg-indigo-600">
                            </span>
                            @endif
                        </a>


                        {{-- Classes --}}
                        <a
                            href="{{ route('student.classes.index') }}"
                            class="relative flex h-full items-center
           px-4 text-sm font-semibold
           transition-all duration-200
           hover:text-indigo-600
           active:scale-95
           {{ request()->routeIs('student.classes.*')
               ? 'text-slate-950'
               : 'text-slate-500' }}">
                            Classes

                            @if(request()->routeIs('student.classes.*'))
                            <span
                                class="absolute inset-x-4 bottom-0
                   h-[3px] rounded-t-full
                   bg-indigo-600">
                            </span>
                            @endif
                        </a>




                        {{-- Performance --}}
                        <a
                            href="{{ url('/student/exam-history') }}#performance"
                            class="relative flex h-full items-center
           px-4 text-sm font-semibold
           text-slate-500
           transition hover:text-indigo-600
           active:scale-95">
                            History
                        </a>
                        <a href="{{ route('student.feedback.index') }}"
   class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">
    Teacher Feedback
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

                        @php
                        $adminNav = [
                        [
                        'label' => 'Overview',
                        'route' => 'admin.dashboard',
                        'active' => 'admin.dashboard',
                        ],
                        [
                        'label' => 'Users',
                        'route' => 'admin.users.index',
                        'active' => 'admin.users.*',
                        ],
                        [
                        'label' => 'Classes',
                        'route' => 'admin.classes.index',
                        'active' => 'admin.classes.*',
                        ],
                        [
                        'label' => 'Exams',
                        'route' => 'admin.exams.index',
                        'active' => 'admin.exams.*',
                        ],
                                                                        [
                        'label' => 'Subjects',
                        'route' => 'admin.subjects.index',
                        'active' => 'admin.subjects.*',
                        ],
                        [
                        'label' => 'Activity Logs',
                        'route' => 'admin.activity-logs.index',
                        'active' => 'admin.activity-logs.*',
                        ],
                        ];
                        @endphp

                        @foreach($adminNav as $item)
                        @php
                        $active = request()->routeIs($item['active']);
                        @endphp

                        <a
                            href="{{ route($item['route']) }}"
                            class="relative flex h-full items-center
                   px-4 text-sm font-semibold
                   transition-all duration-200
                   hover:text-indigo-600
                   active:scale-95
                   {{ $active ? 'text-slate-950' : 'text-slate-500' }}">
                            {{ $item['label'] }}

                            @if($active)
                            <span
                                class="absolute inset-x-4 bottom-0
                           h-[3px] rounded-t-full
                           bg-indigo-600">
                            </span>
                            @endif
                        </a>
                        @endforeach

                        @endif





                    </nav>


                    {{-- Right Commands --}}
                    <div class="flex items-center gap-2">

                        {{-- Teacher Create Exam --}}
                        @if(auth()->user()->role === 'teacher')

                        <a
                            href="{{ url('/teacher/exams') }}"
                            id="aiq-create-exam"
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
                                id="aiq-profile-trigger"
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
                                id="aiq-profile-dropdown"
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
                                    <a
                                        href="{{ route('profile.edit') }}"
                                        class="block rounded-xl px-3 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-indigo-50 hover:text-indigo-700">
                                        Profile settings
                                    </a>

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
                            id="aiq-mobile-toggle"
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
                    id="aiq-mobile-navigation"
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
                            href="{{ route('teacher.classes.index') }}"
                            class="rounded-xl px-4 py-3
                                   text-sm font-semibold
                                   transition-all
                                   hover:bg-slate-100
                                   active:scale-[0.98]">
                            Classes & Subjects
                        </a>
                        <a
    href="{{ route('teacher.subjects.index') }}"
    class="rounded-xl px-4 py-3 text-sm font-semibold transition-all hover:bg-slate-100">
    Subjects
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

        <main id="aiq-main" class="page-enter">

            {{-- Context Header --}}
            @hasSection('page-header')

            @yield('page-header')

            @else

            <div
                id="aiq-context-header"
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

                        <div id="aiq-page-actions" class="mt-4 sm:mt-0">
                            @yield('page-actions')
                        </div>

                        @endif

                    </div>

                </div>

            </div>

            @endif


            {{-- Actual page --}}
            <section
                id="aiq-page-content"
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
                    id="aiq-success-toast"
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
                    id="aiq-error-toast"
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