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
                    'AI-Q Insights is temporarily unavailable because the AI service is busy. Please try again shortly.'
                );
            }

            if ($response->failed()) {
                Log::error('Gemini API request failed.', [
                    'status' => $response->status(),
                    'response' => $response->json(),
                ]);

                throw new RuntimeException(
                    'AI-Q could not generate the AI insight right now.'
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
