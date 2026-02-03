<div
    x-data="{
        show: false,
        message: '',
        timeout: null
    }"
    x-on:toast.window="
        message = $event.detail.message;
        show = true;
        clearTimeout(timeout);
        timeout = setTimeout(() => show = false, 3000);
    "
    x-show="show"
    x-transition
    class="fixed bottom-4 right-4 z-50 max-w-sm rounded-xl
           bg-gray-900 px-4 py-3 text-sm text-white shadow-lg"
    style="display:none;"
>
    <div class="flex items-center gap-2">
        <span class="font-semibold">✔</span>
        <span x-text="message"></span>
    </div>
</div>
