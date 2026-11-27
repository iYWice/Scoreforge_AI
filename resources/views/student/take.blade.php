<h1>{{ $exam->title }}</h1>

<form method="POST" action="">
    @csrf

    @foreach($questions as $question)
        <div>
            <p>{{ $question->question_text }}</p>

            @if($question->question_type == 'mcq')
                @foreach($question->options as $option)
                    <label>
                        <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option->option_text }}">
                        {{ $option->option_text }}
                    </label>
                @endforeach
            @else
                <input type="text" name="answers[{{ $question->id }}]">
            @endif
        </div>
    @endforeach

    <button type="submit">Submit Exam</button>
</form>