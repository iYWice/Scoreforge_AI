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

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="mb-5 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700" role="alert">
                <p class="font-semibold">Please correct the following:</p>
                <ul class="mt-2 list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

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

            {{-- STUDENT ID: REQUIRED FOR STUDENTS --}}
{{-- STUDENT ID --}}
<div id="student-id-wrapper" class="hidden">
    <x-input-label for="student_id" :value="__('Student ID')" />

    <x-text-input
        id="student_id"
        class="mt-1.5 block w-full rounded-lg border-gray-300 px-4 py-2.5 focus:ring-2 focus:ring-indigo-500"
        type="text"
        name="student_id"
        :value="old('student_id')"
        autocomplete="off"
        placeholder="Enter your school student ID"
    />

    <p class="mt-1 text-xs text-gray-500">
        Required for student accounts.
    </p>

    <x-input-error :messages="$errors->get('student_id')" class="mt-1.5" />
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
            <div class="flex flex-col gap-4 pt-2 sm:flex-row sm:items-center sm:justify-between">
                <a
                    href="{{ route('login') }}"
                    class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">
                    Already registered?
                </a>

                <button
                    type="submit"
                    class="inline-flex justify-center rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Register
                </button>
            </div>

            {{-- Show Student ID only when Student is selected --}}
            <script>
                (() => {
                    const roleSelect = document.getElementById('role');
                    const studentIdWrapper = document.getElementById('student-id-wrapper');
                    const studentIdInput = document.getElementById('student_id');

                    function updateStudentIdField() {
                        const isStudent = roleSelect.value === 'student';

                        studentIdWrapper.classList.toggle('hidden', !isStudent);
                        studentIdInput.required = isStudent;
                        studentIdInput.disabled = !isStudent;

                        if (!isStudent) {
                            studentIdInput.value = '';
                        }
                    }

                    roleSelect.addEventListener('change', updateStudentIdField);
                    updateStudentIdField();
                })();
            </script>
        </form>
    </div>
</x-guest-layout>