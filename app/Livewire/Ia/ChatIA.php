<?php

namespace App\Livewire\Ia;

use App\Services\AIRouterService;
use App\Services\DashboardMetricsService;
use App\Services\OpenAIService;
use Livewire\Component;
use Throwable;

class ChatIA extends Component
{
    public string $msg = '';
    public array $history = [];
    public bool $loading = false;

    public function send(): void
    {
        $this->validate(['msg' => 'required|min:3']);

        $this->loading = true;

        try {
            $ia      = app(OpenAIService::class);
            $router  = app(AIRouterService::class);
            $metrics = app(DashboardMetricsService::class);

            $userMessage = trim($this->msg);

            $this->history[] = [
                'type' => 'user',
                'text' => $userMessage,
            ];

            $language = session()->get('locale') === 'en'
                ? 'English'
                : 'Português do Brasil';

            $route = $router->route($userMessage);

            if (($route['mode'] ?? 'MIXED') === 'MIXED') {
                $intent = $ia->classifyIntent($userMessage, $this->history);
                $route['mode'] = $intent; // SYSTEM | AUDIT | MIXED
            }

            if ($route['mode'] === 'SYSTEM') {
                $stats = $metrics->getBasicStats($route['file_service'] ?? null);

                $system = <<<SYS
                    Você é um assistente do dashboard com foco em auditoria e operação.
                    Responda sempre em {$language}, de forma objetiva e direta.
                    REGRAS OBRIGATÓRIAS:
                    - Use SOMENTE os dados do JSON fornecido.
                    - Não invente números.
                    - Se o usuário pedir algo que não existe no JSON, responda: "Não tenho essa métrica disponível no momento."
                    - Se a pergunta for ambígua, peça para o usuário especificar qual métrica (empresas, árvores, fila, processados, falhas).
                SYS;

                $prompt = "JSON (dados do sistema):\n"
                    . json_encode($stats, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
                    . "\n\nPergunta do usuário:\n{$userMessage}";

                $response = $ia->chat([
                    ['role' => 'system', 'content' => $system],
                    ...$ia->historyToMessages($this->history),
                    ['role' => 'user', 'content' => $prompt],
                ], temperature: 0.0);
            } else {
                $system = <<<SYS
                    Você é um auditor contábil.
                    Responda sempre em {$language} e de forma objetiva, conforme CPC/IFRS e boas práticas de auditoria.
                    Se faltar contexto, faça UMA pergunta curta para esclarecer.
                SYS;

                $response = $ia->chat([
                    ['role' => 'system', 'content' => $system],
                    ...$ia->historyToMessages($this->history),
                    ['role' => 'user', 'content' => $userMessage],
                ], temperature: 0.2);
            }

            $this->history[] = [
                'type' => 'IA',
                'text' => $response,
            ];

            $this->msg = '';
        } catch (Throwable $e) {
            logger()->error('Erro no ChatIA', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->history[] = [
                'type' => 'IA',
                'text' => 'Ocorreu um erro ao processar sua solicitação.',
            ];
        } finally {
            $this->loading = false;
        }
    }

    public function render()
    {
        return view('livewire.ia.chat-i-a');
    }
}
