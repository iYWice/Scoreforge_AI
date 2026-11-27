<h1>{{ $exam->title }}</h1>

{-- Collect and submit the data for this operation. --}
<form method="POST" action="">
    @csrf

    {-- Iterate over the related records and render each result. --}
    @foreach($questions as $question)
        <div>
            <p>{{ $question->question_text }}</p>

            {-- Render this content conditionally based on the current view state. --}
            @if($question->question_type == 'mcq')
                {-- Iterate over the related records and render each result. --}
                @foreach($question->options as $option)
                    <label>
                        <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option->option_text }}">
                        {{ $option->option_text }}
                    </label>
                @endforeach
            {-- Render this content conditionally based on the current view state. --}
            @else
                <input type="text" name="answers[{{ $question->id }}]">
            @endif
        </div>
    @endforeach

    <button type="submit">Submit Exam</button>
</form>