@props(['status'])

{-- Render this content conditionally based on the current view state. --}
@if ($status)
    <div {{ $attributes->merge(['class' => 'font-medium text-sm text-green-600 dark:text-green-400']) }}>
        {{ $status }}
    </div>
@endif
