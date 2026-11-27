{-- Use the application layout and configure inherited page structure. --}
@extends('layouts.app')

{-- Define this page section and its presentation content. --}
@section('title', 'Performance Predictions')

{-- Define this page section and its presentation content. --}
@section('content')

<style>
    .aiq-predictions-page { --aiq-purple:#7047df; --aiq-ink:#292437; --aiq-pink:#f4a8e9; --aiq-yellow:#ffe16a; color:var(--aiq-ink); }
    .predictions-hero { border:1.5px solid #33253f !important; border-radius:14px !important; background:linear-gradient(120deg,#ffe16a,#fff1b5 56%,#f2eaff) !important; box-shadow:5px 6px 0 #33253f; }
    .predictions-hero h1 { color:#292437 !important; }
    .predictions-hero p { color:#62576b !important; }
    .predictions-hero a { border:1px solid #30243e; border-radius:9px; background:#7047df !important; box-shadow:0 4px 0 #30243e; }
    .predictions-hero a:hover { background:#5936c0 !important; transform:translateY(-2px); box-shadow:0 6px 0 #30243e; }
    .prediction-card { --tilt-x:0deg; --tilt-y:0deg; border:1.5px solid #382d43 !important; border-radius:12px !important; background:#fffdf8 !important; box-shadow:0 4px 0 #382d43; transform:perspective(900px) rotateX(var(--tilt-x)) rotateY(var(--tilt-y)); transform-style:preserve-3d; transition:transform .16s ease,box-shadow .18s; }
    .prediction-card:hover { transform:perspective(900px) rotateX(var(--tilt-x)) rotateY(var(--tilt-y)) translateY(-3px); box-shadow:0 7px 0 #382d43; }
    .prediction-summary > div:nth-child(1) { background:#e7f3e5 !important; }
    .prediction-summary > div:nth-child(2) { background:#f2eaff !important; }
    .prediction-summary > div:nth-child(3) { background:#f9e4f6 !important; }
    .aiq-predictions-page table thead { background:#f2eaff !important; color:#5937bd !important; }
    .aiq-predictions-page table tbody tr { transition:background .18s; }
    .aiq-predictions-page table tbody tr:hover { background:#fff8dd; }
    .aiq-predictions-page table td,.aiq-predictions-page table th { padding-inline:16px; }
    @media(max-width:640px){.predictions-hero{padding:16px}}
    @media(prefers-reduced-motion:reduce){.aiq-predictions-page *,.aiq-predictions-page *::before,.aiq-predictions-page *::after{transition-duration:.01ms!important;animation-duration:.01ms!important}}
</style>
<div class="aiq-predictions-page max-w-7xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="predictions-hero bg-gradient-to-r from-slate-100 via-indigo-100 to-slate-100
                rounded-2xl p-6 text-slate-900">

        <p class="text-xs uppercase tracking-widest
              text-indigo-600 font-bold">
            Predictive Analytics
        </p>

        <h1 class="text-2xl font-black mt-1 text-slate-900">
            Student Performance Predictions
        </h1>

        <p class="text-sm text-slate-600 mt-2">
            Identify potential exam achievers and students
            who may require additional academic support.
        </p>
        <a
            href="{{ route('teacher.analytics.predictions.export') }}"
            class="aiq-button aiq-button-primary inline-flex rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
            Export CSV
        </a>

    </div>


    {{-- Summary --}}
    <div class="prediction-summary grid grid-cols-1 md:grid-cols-3 gap-5">

        <div class="prediction-card bg-white border border-slate-200 rounded-2xl p-5 aiq-panel aiq-panel-interactive">

            <p class="text-xs uppercase font-bold text-slate-400">
                Likely Achievers
            </p>

            <p class="text-3xl font-black text-emerald-600 mt-2">
                {{ $achievers->count() }}
            </p>

        </div>


        <div class="prediction-card bg-white border border-slate-200 rounded-2xl p-5 aiq-panel aiq-panel-interactive">

            <p class="text-xs uppercase font-bold text-slate-400">
                Potential Achievers
            </p>

            <p class="text-3xl font-black text-indigo-600 mt-2">
                {{ $potentialAchievers->count() }}
            </p>

        </div>


        <div class="prediction-card bg-white border border-slate-200 rounded-2xl p-5 aiq-panel aiq-panel-interactive">

            <p class="text-xs uppercase font-bold text-slate-400">
                At-Risk Students
            </p>

            <p class="text-3xl font-black text-rose-600 mt-2">
                {{ $atRiskStudents->count() }}
            </p>

        </div>

    </div>


    {{-- Achievers --}}
    <div class="prediction-card bg-white rounded-2xl border border-slate-200 aiq-panel aiq-panel-interactive">

        <div class="p-6 border-b border-slate-200">

            <h2 class="font-bold text-lg">
                Predicted Exam Achievers
            </h2>

            <p class="text-xs text-slate-400 mt-1">
                Students with predicted performance of 90% or higher.
            </p>

        </div>

        <div class="p-6 space-y-3">

            {-- Iterate over the related records and render each result. --}
            @forelse($achievers as $prediction)

            <div class="flex items-center justify-between border-b border-slate-100 pb-3">

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
    <div class="prediction-card bg-white rounded-2xl border border-slate-200 aiq-panel aiq-panel-interactive">

        <div class="p-6 border-b border-slate-200">

            <h2 class="font-bold text-lg">
                Students Requiring Attention
            </h2>

            <p class="text-xs text-slate-400 mt-1">
                Students with moderate or high predicted academic risk.
            </p>

        </div>

        <div class="overflow-x-auto">

            {-- Present structured records in a table. --}
            <table class="w-full text-sm">

                {-- Present structured records in a table. --}
                <thead class="bg-slate-50">

                    <tr class="text-left text-xs uppercase
                               text-slate-500 ">

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

                {-- Present structured records in a table. --}
                <tbody class="divide-y divide-slate-100">

                    {-- Iterate over the related records and render each result. --}
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
                                        : 'bg-amber-100 text-amber-700' }} aiq-badge">

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


@push('scripts')
<script>
(() => {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    document.querySelectorAll('.prediction-card').forEach(card => {
        card.addEventListener('pointermove', event => {
            if (event.pointerType === 'touch') return;
            const r = card.getBoundingClientRect();
            card.style.setProperty('--tilt-x', `${(0.5-(event.clientY-r.top)/r.height)*4}deg`);
            card.style.setProperty('--tilt-y', `${((event.clientX-r.left)/r.width-0.5)*6}deg`);
        });
        card.addEventListener('pointerleave', () => { card.style.setProperty('--tilt-x','0deg'); card.style.setProperty('--tilt-y','0deg'); });
    });
})();
</script>
@endpush

@endsection