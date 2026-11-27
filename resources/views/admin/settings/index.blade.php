@extends('layouts.app')

@section('title', 'System Settings | AI-Q')
@section('page-title', 'System Settings')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">

    <div>
        <h1 class="text-2xl font-bold text-slate-900">
            System Settings
        </h1>
        <p class="mt-1 text-sm text-slate-500">
            Configure application-wide AI-Q preferences.
        </p>
    </div>

    @if(session('success'))
    <div class="rounded-xl bg-green-50 p-4 text-sm text-green-700">
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="rounded-xl bg-red-50 p-4 text-sm text-red-700">
        @foreach($errors->all() as $error)
        <p>{{ $error }}</p>
        @endforeach
    </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.update') }}"
        class="space-y-6">
        @csrf
        @method('PATCH')

        <div class="rounded-xl border border-slate-200 bg-white p-6">
            <h2 class="text-lg font-bold text-slate-900">
                General Settings
            </h2>

            <div class="mt-5">
                <label for="system_name"
                    class="block text-sm font-semibold text-slate-700">
                    System Name
                </label>

                <input id="system_name" type="text"
                    name="system_name"
                    value="{{ old('system_name', $settings['system_name']) }}"
                    maxlength="100"
                    required
                    class="mt-2 w-full rounded-xl border-slate-300">

                <p class="mt-2 text-xs text-slate-500">
                    Display name used by AI-Q.
                </p>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6">
            <h2 class="text-lg font-bold text-slate-900">
                Access & Registration
            </h2>

            <div class="mt-5">
                <label class="block text-sm font-semibold text-slate-700">
                    Public Registration
                </label>

                <p class="mt-1 text-xs text-slate-500">
                    Allow new users to register accounts.
                </p>

                <select name="allow_registration"
                    class="mt-3 w-full rounded-xl border-slate-300">
                    <option value="1"
                        @selected(old('allow_registration', $settings['allow_registration'])=='1' )>
                        Enabled
                    </option>
                    <option value="0"
                        @selected(old('allow_registration', $settings['allow_registration'])=='0' )>
                        Disabled
                    </option>
                </select>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6">
            <h2 class="text-lg font-bold text-slate-900">
                Artificial Intelligence
            </h2>

            <div class="mt-5 space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-700">
                        AI Question Builder
                    </label>

                    <p class="mt-1 text-xs text-slate-500">
                        Allow teachers to generate draft questions from learning materials.
                    </p>

                    <select name="ai_question_builder"
                        class="mt-3 w-full rounded-xl border-slate-300">
                        <option value="1"
                            @selected(old('ai_question_builder', $settings['ai_question_builder'])=='1' )>
                            Enabled
                        </option>
                        <option value="0"
                            @selected(old('ai_question_builder', $settings['ai_question_builder'])=='0' )>
                            Disabled
                        </option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700">
                        AI Performance Insights
                    </label>

                    <p class="mt-1 text-xs text-slate-500">
                        Allow new AI-assisted interpretations of examination analytics.
                    </p>

                    <select name="ai_insights"
                        class="mt-3 w-full rounded-xl border-slate-300">
                        <option value="1"
                            @selected(old('ai_insights', $settings['ai_insights'])=='1' )>
                            Enabled
                        </option>
                        <option value="0"
                            @selected(old('ai_insights', $settings['ai_insights'])=='0' )>
                            Disabled
                        </option>
                    </select>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6">
            <h2 class="text-lg font-bold text-slate-900">
                Maintenance
            </h2>

            <div class="mt-5">
                <label class="block text-sm font-semibold text-slate-700">
                    Maintenance Notice
                </label>

                <p class="mt-1 text-xs text-slate-500">
                    Display a maintenance notice without shutting down the system.
                </p>

                <select name="maintenance_notice"
                    class="mt-3 w-full rounded-xl border-slate-300">
                    <option value="0"
                        @selected(old('maintenance_notice', $settings['maintenance_notice'])=='0' )>
                        Disabled
                    </option>
                    <option value="1"
                        @selected(old('maintenance_notice', $settings['maintenance_notice'])=='1' )>
                        Enabled
                    </option>
                </select>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit"
                class="rounded-xl bg-indigo-600 px-6 py-3 text-sm font-semibold text-white hover:bg-indigo-700">
                Save System Settings
            </button>
        </div>
    </form>

</div>
@endsection