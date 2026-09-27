<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class GeminiService
{
    private string $baseUrl =
    'https://generativelanguage.googleapis.com/v1beta';

    public function generate(string $prompt): string
    {
        $apiKey = config('services.gemini.key');

        $model = config(
            'services.gemini.model',
            'gemini-3.8-flash'
        );

        if (empty($apiKey)) {
            throw new RuntimeException(
                'Gemini API key is not configured.'
            );
        }

        try {
            $response = Http::connectTimeout(10)
                ->timeout(45)
                ->retry(1, 1000)
                ->withHeaders([
                    'x-goog-api-key' => $apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post(
                    "{$this->baseUrl}/models/{$model}:generateContent",
                    [
                        'contents' => [
                            [
                                'role' => 'user',
                                'parts' => [
                                    [
                                        'text' => $prompt,
                                    ],
                                ],
                            ],
                        ],

                        'generationConfig' => [
                            'temperature' => 0.2,
                            'maxOutputTokens' => 1200,
                        ],
                    ]
                );

            if ($response->status() === 429) {
                Log::warning('Gemini API quota exceeded.', [
                    'response' => $response->json(),
                ]);

                throw new RuntimeException(
                    'AI-Q has reached its current AI generation limit. Please try again later.'
                );
            }

            if ($response->status() === 503) {
                Log::warning('Gemini API temporarily unavailable.', [
                    'response' => $response->json(),
                ]);

                throw new RuntimeException(
                    'The AI service is temporarily unavailable or busy. Please try again shortly.'
                );
            }

            if ($response->failed()) {
                Log::error('Gemini API request failed.', [
                    'status' => $response->status(),
                    'response' => $response->json(),
                ]);

                throw new RuntimeException(
                    'AI-Q could not complete the AI request right now.'
                );
            }

            $text = $response->json(
                'candidates.0.content.parts.0.text'
            );

            if (empty($text)) {

                Log::warning(
                    'Gemini returned an empty response.',
                    [
                        'response' => $response->json(),
                    ]
                );

                throw new RuntimeException(
                    'Gemini returned an empty response.'
                );
            }

            return trim($text);
        } catch (Throwable $exception) {

            Log::error('Gemini service error.', [
                'message' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }


    /**
     * Simple connection test.
     */

    public function generateQuestions(
        string $sourceText,
        int $count,
        array $types,
        ?string $difficulty = null
    ): array {

        /*
    |--------------------------------------------------------------------------
    | Validate generation settings
    |--------------------------------------------------------------------------
    */

        $count = max(1, min($count, 50));

        $allowedTypes = [
            'multiple_choice',
            'true_false',
            'identification',
        ];

        $types = array_values(
            array_intersect($types, $allowedTypes)
        );

        if (empty($types)) {
            throw new RuntimeException(
                'At least one valid question type is required.'
            );
        }

        if (trim($sourceText) === '') {
            throw new RuntimeException(
                'The learning material contains no readable text.'
            );
        }

        /*
    |--------------------------------------------------------------------------
    | Prevent extremely large prompts
    |--------------------------------------------------------------------------
    */

        $sourceText = mb_substr(
            $sourceText,
            0,
            30000
        );

        $typeList = implode(', ', $types);

        $difficultyInstruction = $difficulty
            ? "Generate questions with {$difficulty} difficulty."
            : 'Use an appropriate mixture of difficulty levels.';

        /*
    |--------------------------------------------------------------------------
    | Gemini Prompt
    |--------------------------------------------------------------------------
    */

        $prompt = <<<PROMPT
You are the AI Question Builder of AI-Q, an examination management
and analytics system used in an academic environment.

Generate examination questions using ONLY the learning material
provided below.

GENERATION SETTINGS:

Number of questions: {$count}

Allowed question types:
{$typeList}

Difficulty instruction:
{$difficultyInstruction}

IMPORTANT RULES:

- Generate exactly {$count} questions.
- Use only information supported by the supplied learning material.
- Do not invent facts that are not found in the material.
- Questions must be clear and academically appropriate.
- Avoid duplicate or nearly identical questions.
- Each question must have one clearly correct answer.
- Assign a concise topic to every question.
- Use 1 point for every generated question.

QUESTION TYPE RULES:

1. multiple_choice
   - Provide exactly four options.
   - Only one option must be correct.
   - correct_answer must contain the complete correct option text.

2. true_false
   - options must contain exactly:
     ["True", "False"]
   - correct_answer must be either "True" or "False".

3. identification
   - options must be an empty array.
   - correct_answer must contain the expected answer.

Return ONLY valid JSON.

Do not include Markdown.
Do not include ```json.
Do not include explanations outside the JSON.

Use exactly this structure:

{
    "questions": [
        {
            "question_type": "multiple_choice",
            "topic": "Topic name",
            "question_text": "Question here",
            "correct_answer": "Correct answer",
            "points": 1,
            "options": [
                "Option 1",
                "Option 2",
                "Option 3",
                "Option 4"
            ]
        }
    ]
}

LEARNING MATERIAL:

{$sourceText}

PROMPT;

        /*
    |--------------------------------------------------------------------------
    | Send to existing Gemini API method
    |--------------------------------------------------------------------------
    */

        $response = $this->generate($prompt);

        /*
    |--------------------------------------------------------------------------
    | Clean possible Markdown fences
    |--------------------------------------------------------------------------
    */

        $response = trim($response);

        $response = preg_replace(
            '/^```(?:json)?\s*/i',
            '',
            $response
        );

        $response = preg_replace(
            '/\s*```$/',
            '',
            $response
        );

        /*
    |--------------------------------------------------------------------------
    | Decode Gemini JSON
    |--------------------------------------------------------------------------
    */

        $decoded = json_decode(
            trim($response),
            true
        );

        if (!is_array($decoded)) {
            throw new RuntimeException(
                'Gemini returned an invalid question format.'
            );
        }

        if (
            !isset($decoded['questions']) ||
            !is_array($decoded['questions'])
        ) {
            throw new RuntimeException(
                'Gemini did not return a question list.'
            );
        }

        /*
    |--------------------------------------------------------------------------
    | Validate generated questions
    |--------------------------------------------------------------------------
    */

        $questions = [];

        foreach ($decoded['questions'] as $question) {

            $questionType =
                $question['question_type'] ?? null;

            if (!in_array(
                $questionType,
                $allowedTypes,
                true
            )) {
                continue;
            }

            if (
                empty($question['question_text']) ||
                empty($question['correct_answer'])
            ) {
                continue;
            }

            $options = $question['options'] ?? [];

            if (!is_array($options)) {
                $options = [];
            }

            $questions[] = [
                'question_type' => $questionType,

                'topic' =>
                trim($question['topic'] ?? 'General'),

                'question_text' =>
                trim($question['question_text']),

                'correct_answer' =>
                trim((string) $question['correct_answer']),

                'points' => 1,

                'options' =>
                array_values($options),
            ];
        }

        if (empty($questions)) {
            throw new RuntimeException(
                'Gemini did not generate any valid questions.'
            );
        }

        return $questions;
    }
    public function testConnection(): string
    {
        return $this->generate(
            'Reply with exactly: AI-Q Gemini connection successful.'
        );
    }



    public function generateExamInsight(
        \App\Models\Exam $exam,
        array $analytics
    ): array {

        $data = [
            'exam' => [
                'title' => $exam->title,
                'subject' => $exam->subject?->name ?? 'Unknown Subject',
            ],

            'statistics' => [
                'total_students' => $analytics['total_students'] ?? 0,
                'total_attempts' => $analytics['total_attempts'] ?? 0,
                'average_score' => $analytics['average_score'] ?? 0,
                'highest_score' => $analytics['highest_score'] ?? 0,
                'lowest_score' => $analytics['lowest_score'] ?? 0,
                'passed' => $analytics['passed'] ?? 0,
                'failed' => $analytics['failed'] ?? 0,
                'pass_rate' => $analytics['pass_rate'] ?? 0,
            ],

            'topic_analysis' => collect(
                $analytics['topic_analysis'] ?? []
            )->map(function ($topic) {
                return [
                    'topic' => $topic['topic'],
                    'accuracy' => $topic['accuracy'],
                    'correct' => $topic['correct'],
                    'total' => $topic['total'],
                ];
            })->values()->toArray(),

            'difficult_questions' => collect(
                $analytics['question_analysis'] ?? []
            )
                ->filter(
                    fn($question) => ($question['difficulty'] ?? null) === 'Difficult'
                )
                ->sortBy('accuracy')
                ->take(5)
                ->map(function ($question) {
                    return [
                        'topic' => $question['topic'] ?? 'General',
                        'accuracy' => $question['accuracy'],
                        'correct' => $question['correct'],
                        'incorrect' => $question['incorrect'],
                    ];
                })
                ->values()
                ->toArray(),
        ];

        $json = json_encode(
            $data,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        );

        $prompt = <<<PROMPT
You are the AI analysis component of AI-Q, an examination
management and analytics system used in an academic environment.

Analyze the examination analytics provided below.

IMPORTANT RULES:
- Do not recalculate or modify the supplied statistics.
- Treat the supplied numerical values as the authoritative
  calculations produced by AI-Q.
- Do not invent student information, topics, questions, scores,
  causes, or statistics.
- Do not diagnose students.
- Do not claim that a weakness is caused by a specific factor
  unless the supplied data demonstrates it.
- Base every finding on the supplied examination data.
- Recommendations must be practical and appropriate for a teacher.
- Keep the analysis concise and professional.
- Keep the performance summary to a maximum of 3 sentences.
- Return a maximum of 4 key findings.
- Return a maximum of 4 recommended actions.
- Keep each finding and recommendation concise.

Return ONLY valid JSON.

Use exactly this structure:

{
    "performance_summary": "A concise overall interpretation.",
    "key_findings": [
        "Finding supported by the supplied analytics",
        "Finding supported by the supplied analytics"
    ],
    "recommended_actions": [
        "Practical instructional recommendation",
        "Practical instructional recommendation"
    ]
}

If there is insufficient data, clearly say so in the relevant
fields instead of inventing an analysis.

EXAMINATION DATA:

{$json}
PROMPT;

        $response = $this->generate($prompt);

        /*
    |--------------------------------------------------------------------------
    | Remove accidental Markdown fences
    |--------------------------------------------------------------------------
    */

        $response = trim($response);

        $response = preg_replace(
            '/^```(?:json)?\s*/i',
            '',
            $response
        );

        $response = preg_replace(
            '/\s*```$/',
            '',
            $response
        );

        $decoded = json_decode(
            trim($response),
            true
        );

        if (!is_array($decoded)) {
            throw new \RuntimeException(
                'Gemini returned an invalid exam insight format.'
            );
        }

        return [
            'performance_summary' =>
            $decoded['performance_summary']
                ?? 'No performance summary was generated.',

            'key_findings' =>
            is_array($decoded['key_findings'] ?? null)
                ? $decoded['key_findings']
                : [],

            'recommended_actions' =>
            is_array($decoded['recommended_actions'] ?? null)
                ? $decoded['recommended_actions']
                : [],
        ];
    }
}
