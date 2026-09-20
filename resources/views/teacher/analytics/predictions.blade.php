@extends('layouts.app')

@section('title', 'Performance Predictions')

@section('content')

<div class="max-w-7xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900
                rounded-2xl p-6 text-white">

        <p class="text-xs uppercase tracking-widest
                  text-indigo-400 font-bold">
            Predictive Analytics
        </p>

        <h1 class="text-2xl font-black mt-1">
            Student Performance Predictions
        </h1>

        <p class="text-sm text-slate-400 mt-2">
            Identify potential exam achievers and students
            who may require additional academic support.
        </p>

    </div>


    {{-- Summary --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

        <div class="bg-white dark:bg-slate-900
                    border border-slate-200 dark:border-slate-800
                    rounded-2xl p-5">

            <p class="text-xs uppercase font-bold text-slate-400">
                Likely Achievers
            </p>

            <p class="text-3xl font-black text-emerald-600 mt-2">
                {{ $achievers->count() }}
            </p>

        </div>


        <div class="bg-white dark:bg-slate-900
                    border border-slate-200 dark:border-slate-800
                    rounded-2xl p-5">

            <p class="text-xs uppercase font-bold text-slate-400">
                Potential Achievers
            </p>

            <p class="text-3xl font-black text-indigo-600 mt-2">
                {{ $potentialAchievers->count() }}
            </p>

        </div>


        <div class="bg-white dark:bg-slate-900
                    border border-slate-200 dark:border-slate-800
                    rounded-2xl p-5">

            <p class="text-xs uppercase font-bold text-slate-400">
                At-Risk Students
            </p>

            <p class="text-3xl font-black text-rose-600 mt-2">
                {{ $atRiskStudents->count() }}
            </p>

        </div>

    </div>


    {{-- Achievers --}}
    <div class="bg-white dark:bg-slate-900
                rounded-2xl border
                border-slate-200 dark:border-slate-800">

        <div class="p-6 border-b
                    border-slate-200 dark:border-slate-800">

            <h2 class="font-bold text-lg">
                Predicted Exam Achievers
            </h2>

            <p class="text-xs text-slate-400 mt-1">
                Students with predicted performance of 90% or higher.
            </p>

        </div>

        <div class="p-6 space-y-3">

            @forelse($achievers as $prediction)

            <div class="flex items-center justify-between
                            border-b border-slate-100
                            dark:border-slate-800 pb-3">

                <div>

                    <p class="font-bold">
                        {{ $prediction->student->fname }}
                        {{ $prediction->student->lname }}
                    </p>

                    <p class="text-xs text-slate-400">
                        Confidence:
                        {{ $prediction->confidence_level }}
                    </p>

                </div>

                <div class="text-right">

                    <p class="font-black text-emerald-600">
                        {{ number_format(
                                $prediction->predicted_score,
                                1
                            ) }}%
                    </p>

                    <span class="text-xs text-emerald-600 font-bold">
                        Likely Achiever
                    </span>

                </div>

            </div>

            @empty

            <p class="text-sm text-slate-400">
                No likely achievers identified yet.
            </p>

            @endforelse

        </div>

    </div>


    {{-- At Risk --}}
    <div class="bg-white dark:bg-slate-900
                rounded-2xl border
                border-slate-200 dark:border-slate-800">

        <div class="p-6 border-b
                    border-slate-200 dark:border-slate-800">

            <h2 class="font-bold text-lg">
                Students Requiring Attention
            </h2>

            <p class="text-xs text-slate-400 mt-1">
                Students with moderate or high predicted academic risk.
            </p>

        </div>

        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="bg-slate-50 dark:bg-slate-950">

                    <tr class="text-left text-xs uppercase
                               text-slate-500">

                        <th class="px-6 py-3">
                            Student
                        </th>

                        <th class="px-6 py-3">
                            Predicted
                        </th>

                        <th class="px-6 py-3">
                            Risk
                        </th>

                        <th class="px-6 py-3">
                            Confidence
                        </th>

                        <th class="px-6 py-3">
                            Reason
                        </th>

                    </tr>

                </thead>

                <tbody class="divide-y
                              divide-slate-100
                              dark:divide-slate-800">

                    @forelse($atRiskStudents as $prediction)

                    <tr>

                        <td class="px-6 py-4 font-semibold">

                            {{ $prediction->student->fname }}
                            {{ $prediction->student->lname }}

                        </td>

                        <td class="px-6 py-4 font-bold">

                            {{ number_format(
                                    $prediction->predicted_score,
                                    1
                                ) }}%

                        </td>

                        <td class="px-6 py-4">

                            <span class="px-2.5 py-1
                                    rounded-full text-xs font-bold
                                    {{ $prediction->risk_level === 'High'
                                        ? 'bg-rose-100 text-rose-700'
                                        : 'bg-amber-100 text-amber-700' }}">

                                {{ $prediction->risk_level }}

                            </span>

                        </td>

                        <td class="px-6 py-4">

                            {{ $prediction->confidence_level }}

                        </td>

                        <td class="px-6 py-4
                                       text-xs text-slate-500
                                       max-w-md">

                            {{ $prediction->reason }}

                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td colspan="5"
                            class="text-center py-10
                                       text-slate-400">

                            No at-risk students identified.

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection