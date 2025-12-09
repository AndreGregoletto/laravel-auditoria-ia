<div class="w-full max-w-3xl mx-auto">

    <h3 class="text-lg font-semibold mb-4 text-center">
        Consulta Inteligente de Auditoria
    </h3>

    <!-- Histórico -->
    <div class="bg-gray-100 dark:bg-gray-900 p-4 rounded-lg h-96 overflow-y-auto mb-4 space-y-3">

        @foreach ($history as $msg)
            <div class="p-3 rounded-lg text-sm
                {{ $msg['type'] === 'user'
                    ? 'bg-blue-500 text-white ml-auto w-3/4'
                    : 'bg-white dark:bg-gray-700 text-gray-800 dark:text-white mr-auto w-3/4' }}">
                {!! nl2br(e($msg['text'])) !!}
            </div>
        @endforeach

        @if($loading)
            <div class="text-center text-sm text-gray-500">
                Processando auditoria...
            </div>
        @endif
    </div>

    <!-- Campo de envio -->
    <form wire:submit.prevent="send" class="flex gap-2">

        <input
            type="text"
            wire:model.defer="msg"
            class="flex-1 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800"
            placeholder="Digite sua pergunta de auditoria..."
        />

        <button
            type="submit"
            class="px-4 py-2 bg-indigo-600 text-white rounded-md"
            wire:loading.attr="disabled"
        >
            Enviar
        </button>

    </form>
</div>
