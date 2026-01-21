<?php

namespace App\AI\Intent;

use App\Services\OpenAIService;

class IntentEngine
{
    public function __construct(
        protected OpenAIService $ai
    )
    {

    }

    public function detect(string $message): IntentResult
    {
        $prompt = str_replace(
            '{{question}}',
            $message,
            file_get_contents(
                app_path('AI/Intent/prompts/intent-classifier.txt')
            )
        );

        $response = $this->ai->chat([
            [
                'role'    => 'system',
                'content' => 'Responda apenas com JSON válido.'
            ], [
                'role'    => 'user',
                'content' => $prompt
            ],

        ], temperature: 0.0);

        $json = json_decode($response, true);

        if(!is_array($json)){
            return new IntentResult(
                IntentType::UNKNOWN, [], 0.0, $response
            );
        }

        return new IntentResult(
            intent: IntentType::tryFrom($json['intent'] ?? '') ?? IntentType::UNKNOWN,
            filters: $json['filters'] ?? [],
            focus: $json['focus'] ?? null,
            raw: $response,
            confidence: (float) ($json['confidence'] ?? 0),
        );

    }
}
