@extends('layouts.app')

@section('title', 'My Profile')
@section('eyebrow', 'Account Settings')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">

    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <div class="mb-6">
            <h2 class="text-lg font-bold text-slate-900">Profile information</h2>
            <p class="mt-1 text-sm text-slate-500">
                Update your name and email address.
            </p>
        </div>

        @if (session('status') === 'profile-updated')
        <div class="mb-5 rounded-xl bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            Profile saved.
        </div>
        @endif

        <form method="POST" action="{{ route('profile.update') }}" class="space-y-5">
            @csrf
            @method('PATCH')

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="fname" class="mb-1 block text-sm font-semibold text-slate-700">
                        First name
                    </label>
                    <input
                        id="fname"
                        name="fname"
                        type="text"
                        value="{{ old('fname', $user->fname) }}"
                        required
                        maxlength="255"
                        autocomplete="given-name"
                        class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                    @error('fname')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="lname" class="mb-1 block text-sm font-semibold text-slate-700">
                        Last name
                    </label>
                    <input
                        id="lname"
                        name="lname"
                        type="text"
                        value="{{ old('lname', $user->lname) }}"
                        required
                        maxlength="255"
                        autocomplete="family-name"
                        class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                    @error('lname')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="email" class="mb-1 block text-sm font-semibold text-slate-700">
                    Email address
                </label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email', $user->email) }}"
                    required
                    maxlength="255"
                    autocomplete="email"
                    class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <span class="mb-1 block text-sm font-semibold text-slate-700">Account role</span>
                <p class="text-sm capitalize text-slate-600">{{ $user->role }}</p>
                <p class="mt-1 text-xs text-slate-500">Your role can’t be changed from this page.</p>
            </div>

            <button
                type="submit"
                class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">
                Save profile
            </button>
        </form>
    </section>

</div>
@endsection