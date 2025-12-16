<div class="flex items-center space-x-2 border-r border-gray-200 dark:border-gray-700 pr-4 mr-2">
    <button
        wire:click="changeLocale('pt_BR')"
        class="text-xs font-bold p-1 rounded {{ app()->getLocale() == 'pt_BR' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300' : 'text-gray-400 hover:text-gray-600' }}"
    >
        BR
    </button>
    <span class="text-gray-300 dark:text-gray-600">|</span>
    <button
        wire:click="changeLocale('en')"
        class="text-xs font-bold p-1 rounded {{ app()->getLocale() == 'en' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300' : 'text-gray-400 hover:text-gray-600' }}"
    >
        EN
    </button>
</div>
