<?php

namespace App\Livewire\Ia;

use App\AI\Formatters\AIFormatter;
use App\AI\Intent\IntentEngine;
use App\AI\Resolvers\ResolverFactory;
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
            $userMessage = trim($this->msg);
            $this->history[] = [
                'type' => 'user',
                'text' => $userMessage,
            ];

            $language     = session()->get('locale') === 'en' ? 'English' : 'Portugês do Brasil';
            $intentEngine = app(IntentEngine::class);
            $formatter    = app(AIFormatter::class);

            $intent   = $intentEngine->detect($userMessage);
            $resolver = ResolverFactory::make($intent->intent);

            if($resolver){
                $data     = $resolver->resolve($intent);
                $response = $formatter->explain($data, $language);
            }else{
                $response = app(OpenAIService::class)->chat([
                    [
                        'role' => 'system',
                        'content' => "Vocé é um auditor contábil. Responda em {$language}."
                    ],['role' => 'user', 'content' => $userMessage],
                ], temperature: 0.2);
            }

            $this->history[] = [
                'type' => 'IA',
                'text' => $response
            ];

            $this->msg = '';
        } catch (Throwable $e) {
            logger()->error('Erro no ChatIA', [
                'message' => $e->getMessage(),
                'class'   => get_class($e),
            ]);

            $this->history[] = [
                'type' => 'IA',
                'text' => 'Erro ao processar sua solicitação.',
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
