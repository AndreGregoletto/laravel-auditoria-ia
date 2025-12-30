<?php

namespace App\Livewire\Layout;

use App\Models\UserNotification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class HeaderNotifications extends Component
{
    public int $unreadCount = 0;
    public array $items     = [];

    public function mount(): void
    {
        $this->refreshNotifications();
    }

    public function refreshNotifications(): void
    {
        $iUser = Auth::id();

        $this->unreadCount = UserNotification::query()
            ->where('user_id', 1)
            ->where('status', 1)
            ->where('read', 0)
            ->count();

        $this->items = UserNotification::query()
            ->where('user_id', 1)
            ->where('status', 1)
            ->where('read', 0)
            ->limit(5)
            ->get(['id', 'message', 'read', 'file_id', 'created_at'])
            ->map(fn ($n) => [
                'id'         => $n->id,
                'message'    => $n->message,
                'read'       => (bool) $n->read,
                'file_id'    => $n->file_id,
                'created_at' => optional($n->created_at)->toDateTimeString(),
            ])->toArray();
//        dd($this->items );
    }

    public function markAsRead(int $id): void
    {
        UserNotification::query()
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->update(['read' => 1]);

        $this->refreshNotifications();
    }

    public function clearAll(): void
    {
        UserNotification::query()
            ->where('user_id', Auth::id())
            ->where('status', 1)
            ->update(['read' => 1]);

        $this->refreshNotifications();
    }

    public function render()
    {
        return view('livewire.layout.header-notifications');
    }
}
