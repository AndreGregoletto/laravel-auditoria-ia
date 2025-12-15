<div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
    <h3 class="text-lg font-semibold mb-4">
        {{ __('navbar.my_uploaded_files') }}
    </h3>

    <table class="w-full text-md">
        <thead>
        <tr class="text-left text-gray-500">
            <th>Arquivo</th>
            <th>Extensão</th>
            <th>Tamanho</th>
            <th>Processamento</th>
            <th>Status</th>
            <th>Enviado Em</th>
            <th>Ações</th>
        </tr>
        </thead>

        <tbody>
        @forelse($files as $file)
            <tr class="border-t">
                <td>{{ $file->file_name }}</td>
                <td>{{ $file->file_extension }}</td>
                <td>{{ number_format($file->file_size / 1024, 1) }} KB</td>
                <td>
                    @switch($file->file_step)
                        @case(1) <span class="text-yellow-500">Processando</span> @break
                        @case(2) <span class="text-green-500">Processado</span> @break
                        @case(3) <span class="text-red-500">Erro</span> @break
                        @case(4) <span class="text-gray-500">Cancelado</span> @break
                        @default <span class="text-blue-400">Na Fila</span>
                    @endswitch
                </td>

                <td class="{{ $file->status === 1 ? 'text-green-500' : 'text-red-500' }}">{{ $file->status === 1 ? 'Ativo' : 'Inativo' }}</td>

                <td>{{ $file->created_at->translatedFormat('d F Y, H:i') }}</td>
                <td class="space-x-2">
                    @if($file->file_step === 0 && $file->status === 1)
                        <button wire:click="cancel({{ $file->id }})"
                                class="text-red-500 hover:underline">
                            Cancelar
                        </button>

                        <button wire:click="$emit('replaceFile', {{ $file->id }})"
                                class="text-indigo-500 hover:underline">
                            Substituir
                        </button>
                    @endif
                </td>
            </tr>

        @empty
            <tr>
                <td colspan="4" class="text-center py-6 text-gray-400">
                    Nenhum arquivo enviado ainda.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>
