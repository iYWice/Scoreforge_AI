<div>
    <div class="mb-6">
        <h2 class="text-lg font-bold text-slate-900">Update password</h2>
        <p class="mt-1 text-sm text-slate-500">
            Use at least 8 characters, including letters and numbers.
        </p>
    </div>

    @if (session('status') === 'password-updated')
    <div class="mb-5 rounded-xl bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
        Password updated successfully.
    </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label for="update_password_current_password" class="mb-1 block text-sm font-semibold text-slate-700">
                Current password
            </label>
            <input
                id="update_password_current_password"
                name="current_password"
                type="password"
                autocomplete="current-password"
                required
                class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
            @foreach ($errors->updatePassword->get('current_password') as $message)
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @endforeach
        </div>

        <div>
            <label for="update_password_password" class="mb-1 block text-sm font-semibold text-slate-700">
                New password
            </label>
            <input
                id="update_password_password"
                name="password"
                type="password"
                autocomplete="new-password"
                required
                class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
            @foreach ($errors->updatePassword->get('password') as $message)
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @endforeach
        </div>

        <div>
            <label for="update_password_password_confirmation" class="mb-1 block text-sm font-semibold text-slate-700">
                Confirm new password
            </label>
            <input
                id="update_password_password_confirmation"
                name="password_confirmation"
                type="password"
                autocomplete="new-password"
                required
                class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        <button
            type="submit"
            class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">
            Update password
        </button>
    </form>
</div>
