<x-guest-layout>
    <div class="w-full sm:max-w-md mt-6 px-8 py-9 bg-white dark:bg-slate-900 shadow-xl shadow-slate-900/10 dark:shadow-slate-950/50 rounded-2xl border border-slate-200/80 dark:border-slate-800 transition-all duration-300">

        {{-- HEADER SECTION --}}
        <div class="mb-8 text-center">
            <div class="inline-flex items-center justify-center w-12 h-12 bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 rounded-xl mb-3 shadow-sm border border-indigo-100 dark:border-indigo-900/50">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457-.39-2.823-1.07-4" />
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-slate-100 tracking-tight">
                {{ __('Welcome Back') }}
            </h2>
            <p class="mt-1.5 text-xs font-medium text-slate-500 dark:text-slate-400">
                {{ __('Please sign in to access your examination portal.') }}
            </p>
        </div>

        <x-auth-session-status class="mb-5" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            {{-- EMAIL --}}
            <div>
                <x-input-label for="email" :value="__('Email Address')" class="text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300" />
                <x-text-input
                    id="email"
                    class="block mt-1.5 w-full px-4 py-2.5 rounded-xl border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-sm font-medium focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all duration-200"
                    type="email"
                    name="email"
                    :value="old('email')"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="you@example.com" />
                <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
            </div>

            {{-- PASSWORD --}}
            <div>
                <div class="flex items-center justify-between">
                    <x-input-label for="password" :value="__('Password')" class="text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300" />

                    @if (Route::has('password.request'))
                    <a class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 hover:underline underline-offset-4 transition-colors" href="{{ route('password.request') }}">
                        {{ __('Forgot password?') }}
                    </a>
                    @endif
                </div>

                <x-text-input
                    id="password"
                    class="block mt-1.5 w-full px-4 py-2.5 rounded-xl border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 text-sm font-medium focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all duration-200"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••" />
                <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
            </div>

            {{-- REMEMBER ME --}}
            <div class="flex items-center justify-between pt-1">
                <label for="remember_me" class="inline-flex items-center cursor-pointer select-none group">
                    <input
                        id="remember_me"
                        type="checkbox"
                        class="rounded-md h-4 w-4 bg-slate-50 dark:bg-slate-950 border-slate-300 dark:border-slate-800 text-indigo-600 shadow-sm focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900 transition duration-150 ease-in-out cursor-pointer"
                        name="remember">
                    <span class="ms-2.5 text-xs font-medium text-slate-600 dark:text-slate-400 group-hover:text-slate-900 dark:group-hover:text-slate-200 transition-colors">
                        {{ __('Remember me on this device') }}
                    </span>
                </label>
            </div>

            {{-- ACTIONS --}}
            <div class="pt-3">
                <x-primary-button class="w-full justify-center px-5 py-3 text-xs font-bold uppercase tracking-wider bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 text-white rounded-xl shadow-md shadow-indigo-500/20 transition-all duration-200 ease-[cubic-bezier(0.34,1.56,0.64,1)] hover:scale-[1.02] active:scale-95 cursor-pointer">
                    {{ __('Sign In') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>