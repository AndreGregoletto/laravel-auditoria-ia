<?php

namespace App\Livewire\Reports\Profile\Message;

use App\Models\UserNotification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        $msg = UserNotification::query()
            ->with([
                'file.type_file',
                'file.company',
                'message',
            ])
            ->where('user_id', Auth::id())
            ->get()
            ->map(fn ($row) => [
                'read'       => $row->read                    ?? '',
                'file_id'    => $row->file_id                 ?? '',
                'message'    => $row->message                 ?? '',
                'message_id' => $row->message_id              ?? '',
                'created_at' => $row->created_at              ?? '',
                'updated_at' => $row->updated_at              ?? '',
                'file_name'  => $row->file?->file_name        ?? '',
                'type_file'  => $row->file?->type_file?->name ?? '',
                'company'    => $row->file?->company?->name   ?? '',
                'msg_system' => $row->message?->name          ?? '',
            ]);

        return view('livewire.reports.profile.message.index', compact('msg'))->layout('layouts.app');
    }
}
