<?php

namespace App\AI\Formatters;

use App\Services\OpenAIService;

class AIFormatter
{
    public function __construct(
        protected OpenAIService $ai
    ) {}

    public function explain(
        array $data,
        string $language = 'Português do Brasil'
    ): string {
        $system = <<<SYS
            Você é um assistente de auditoria.

            Explique os dados abaixo de forma clara e objetiva.
            Não invente números.
            Não faça suposições.
            Use apenas o JSON fornecido.
            Responda em {$language}.
        SYS;

        return $this->ai->chat([
            ['role' => 'system', 'content' => $system],
            [
                'role' => 'user',
                'content' =>
                    "DADOS DO SISTEMA:\n"
                    . json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            ],
        ], temperature: 0.0);
    }
}
