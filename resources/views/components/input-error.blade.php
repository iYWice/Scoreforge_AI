@props(['messages'])

{-- Render this content conditionally based on the current view state. --}
@if ($messages)
    <ul {{ $attributes->merge(['class' => 'text-sm text-red-600 dark:text-red-400 space-y-1']) }}>
        {-- Iterate over the related records and render each result. --}
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif
