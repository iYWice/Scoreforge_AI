<x-guest-layout>
    <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-xl border border-gray-100 dark:border-gray-700">
        
        {{-- HEADER SECTION --}}
        <div class="mb-6 text-center">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                {{ __('Create an Account') }}
            </h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Join us by filling out the information below.') }}
            </p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            {{-- NAME GRID --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- FIRST NAME --}}
                <div>
                    <x-input-label for="fname" :value="__('First Name')" class="font-medium" />
                    <x-text-input 
                        id="fname" 
                        class="block mt-1.5 w-full px-4 py-2.5 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-2 focus:ring-indigo-500" 
                        type="text" 
                        name="fname" 
                        :value="old('fname')" 
                        required 
                        autofocus 
                        autocomplete="given-name" 
                        placeholder="John"
                    />
                    <x-input-error :messages="$errors->get('fname')" class="mt-1.5" />
                </div>

                {{-- LAST NAME --}}
                <div>
                    <x-input-label for="lname" :value="__('Last Name')" class="font-medium" />
                    <x-text-input 
                        id="lname" 
                        class="block mt-1.5 w-full px-4 py-2.5 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-2 focus:ring-indigo-500" 
                        type="text" 
                        name="lname" 
                        :value="old('lname')" 
                        required 
                        autocomplete="family-name" 
                        placeholder="Doe"
                    />
                    <x-input-error :messages="$errors->get('lname')" class="mt-1.5" />
                </div>
            </div>

            {{-- EMAIL --}}
            <div>
                <x-input-label for="email" :value="__('Email Address')" class="font-medium" />
                <x-text-input 
                    id="email" 
                    class="block mt-1.5 w-full px-4 py-2.5 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-2 focus:ring-indigo-500" 
                    type="email" 
                    name="email" 
                    :value="old('email')" 
                    required 
                    autocomplete="username" 
                    placeholder="you@example.com"
                />
                <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
            </div>

            {{-- ROLE --}}
            <div>
                <x-input-label for="role" :value="__('Select Your Role')" class="font-medium" />
                <select 
                    id="role" 
                    name="role" 
                    required 
                    class="block mt-1.5 w-full px-4 py-2.5 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 rounded-lg shadow-sm transition duration-150 ease-in-out"
                >
                    <option value="" disabled {{ old('role') ? '' : 'selected' }}>
                        Choose a role...
                    </option>
                    <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>
                        Student
                    </option>
                    <option value="teacher" {{ old('role') == 'teacher' ? 'selected' : '' }}>
                        Teacher
                    </option>
                </select>
                <x-input-error :messages="$errors->get('role')" class="mt-1.5" />
            </div>

            {{-- PASSWORD --}}
            <div>
                <x-input-label for="password" :value="__('Password')" class="font-medium" />
                <x-text-input 
                    id="password" 
                    class="block mt-1.5 w-full px-4 py-2.5 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-2 focus:ring-indigo-500" 
                    type="password" 
                    name="password" 
                    required 
                    autocomplete="new-password" 
                    placeholder="••••••••"
                />
                <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
            </div>

            {{-- CONFIRM PASSWORD --}}
            <div>
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="font-medium" />
                <x-text-input 
                    id="password_confirmation" 
                    class="block mt-1.5 w-full px-4 py-2.5 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-2 focus:ring-indigo-500" 
                    type="password" 
                    name="password_confirmation" 
                    required 
                    autocomplete="new-password" 
                    placeholder="••••••••"
                />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
            </div>

            {{-- ACTIONS --}}
            <div class="flex flex-col sm:flex-row items-center justify-between pt-2 gap-4">
                <a class="text-sm text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition duration-150 ease-in-out underline underline-offset-4" href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>

                <x-primary-button class="w-full sm:w-auto justify-center px-5 py-2.5 text-sm tracking-wide bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-900 focus:ring-indigo-500 rounded-lg">
                    {{ __('Register') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>