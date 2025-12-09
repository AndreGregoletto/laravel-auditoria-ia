<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OpenAIService
{
    public function askTo(string $prompt): string
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.openai.key'),
            'Content-Type'  => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => config('services.openai.model'),
            'messages' => [
                ['role' => 'system', 'content' => 'Você é um auditor financeiro profissional.'],
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0.2
        ]);

        if (!$response->successful()) {
            logger()->error('Erro OpenAI', $response->json());
            return 'Erro ao consultar IA.';
        }

        return $response->json()['choices'][0]['message']['content'] ?? 'Sem resposta da IA';
    }
}
