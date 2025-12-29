<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OpenAIService
{
    public function chat(array $messages, float $temperature = 0.2): string
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.openai.key'),
            'Content-Type'  => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model'       => config('services.openai.model'),
            'messages'    => $messages,
            'temperature' => $temperature,
        ]);

        if (!$response->successful()) {
            logger()->error('Erro OpenAI', [
                'status' => $response->status(),
                'body'   => $response->body(),
                'json'   => $response->json(),
            ]);
            return 'Erro ao consultar IA.';
        }

        return $response->json('choices.0.message.content') ?? 'Sem resposta da IA';
    }

    public function askTo(string $prompt, ?string $system = null, float $temperature = 0.2): string
    {
        $system = $system ?: 'Você é um auditor contábil geral.';
        return $this->chat([
            ['role' => 'system', 'content' => $system],
            ['role' => 'user', 'content' => $prompt],
        ], $temperature);
    }

    public function historyToMessages(array $history, int $takeLast = 8): array
    {
        return collect($history)
            ->take(-$takeLast)
            ->map(function ($h) {
                $type = $h['type'] ?? 'user';
                $text = (string)($h['text'] ?? '');
                return [
                    'role'    => $type === 'IA' ? 'assistant' : 'user',
                    'content' => $text,
                ];
            })
            ->values()
            ->all();
    }

    public function classifyIntent(string $message, array $history = []): string
    {
        $recent = collect($history)
            ->take(-8)
            ->map(fn ($h) => (($h['type'] ?? '') === 'IA' ? 'IA: ' : 'Usuário: ') . ($h['text'] ?? ''))
            ->implode("\n");

        $system = 'Você é um classificador de intenções. Responda SOMENTE com: SYSTEM, AUDIT ou MIXED.';
        $prompt = <<<TXT
            Classifique a intenção da pergunta do usuário em UMA das categorias:

            - SYSTEM: perguntas sobre dados/estatísticas do sistema (contagens, fila, empresas, árvores, importações, status, jobs).
            - AUDIT: perguntas sobre CPC/IFRS, normas contábeis, procedimentos de auditoria, explicações teóricas.
            - MIXED: mistura das duas.

            Regras:
            - Responda SOMENTE com: SYSTEM ou AUDIT ou MIXED.
            - Se houver dúvida, escolha SYSTEM quando a pergunta pedir números/listagens do sistema.

            Contexto recente:
            {$recent}

            Pergunta:
            {$message}
        TXT;

        $raw = $this->askTo($prompt, $system, temperature: 0.0);
        $raw = strtoupper(trim((string)$raw));
        $raw = preg_replace('/[^A-Z]/', '', $raw) ?: $raw;

        return match (true) {
            str_contains($raw, 'SYSTEM') => 'SYSTEM',
            str_contains($raw, 'AUDIT')  => 'AUDIT',
            str_contains($raw, 'MIXED')  => 'MIXED',
            default => 'AUDIT',
        };
    }
}
