<?php

namespace App\Livewire\Ia;

use App\Services\OpenAIService;
use Livewire\Component;

class ChatIA extends Component
{
    public string $msg = '';
    public array $history = [];
    public bool $loading = false;

    public function send(OpenAIService $ia)
    {
        $this->validate(['msg' => 'required|min:3']);

        $this->loading = true;

        $this->history[] = [
            'type' => 'user',
            'text' => $this->msg
        ];

        $lenguage = session()->get('locale') == 'en' ? 'Inglês' : 'Português do Brasil';

        $prompt = "
            Você é um auditor contábil.
            Responda sempre em {$lenguage} e de forma objetiva, conforme normas e pronunciamentos contábeis.

            Pergunta: {$this->msg}";

        $response = $ia->askTo($prompt);

        $this->history[] = [
            'type' => 'IA',
            'text' => $response
        ];

        $this->msg = '';
        $this->loading = false;
    }

    public function render()
    {
        return view('livewire.ia.chat-i-a');
    }
}
