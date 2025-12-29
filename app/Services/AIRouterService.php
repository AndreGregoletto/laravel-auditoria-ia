<?php

namespace App\Services;

class AIRouterService
{
    public function route(string $message): array
    {
        $norm = $this->normalize($message);

        $systemKeywords = [
            'quantas', 'quantos', 'total', 'numero', 'número',
            'cadastrad', 'registrad', 'exist', 'tem',
            'empresa', 'empresas',
            'arvore', 'árvore', 'arvores', 'árvores',
            'fila', 'queue', 'job', 'jobs', 'processad', 'pendente',
            'enviado', 'import', 'importacao', 'importação'
        ];

        $isSystem = $this->containsAny($norm, $systemKeywords);

        if (!$isSystem) {
            return [
                'mode' => 'AUDIT',
                'metrics' => [],
                'file_service' => null,
            ];
        }

        $metrics = [
            'companies' => str_contains($norm, 'empresa'),
            'trees'     => (str_contains($norm, 'arvore') || str_contains($norm, 'árvore')),
            'queue'     => (str_contains($norm, 'fila') || str_contains($norm, 'job') || str_contains($norm, 'queue') || str_contains($norm, 'import')),
        ];

        if (!in_array(true, $metrics, true)) {
            return [
                'mode' => 'MIXED',
                'metrics' => $metrics,
                'file_service' => null,
            ];
        }

        return [
            'mode'         => 'SYSTEM',
            'metrics'      => $metrics,
            'file_service' => null,
        ];
    }

    private function containsAny(string $haystack, array $needles): bool
    {
        foreach ($needles as $n) {
            if (str_contains($haystack, $this->normalize($n))) {
                return true;
            }
        }
        return false;
    }

    private function normalize(string $text): string
    {
        $text = mb_strtolower(trim($text));

        $converted = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
        if ($converted !== false && $converted !== null) {
            $text = $converted;
        }

        $text = preg_replace('/[^a-z0-9\s]/', ' ', $text);
        return preg_replace('/\s+/', ' ', $text) ?? $text;
    }
}
