<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserNotification;

class NotificationsService
{
    public function sendByProcess(
        string $message, int $idMessage, int $fileId, int $id
    ): void
    {
        $m   = empty($message)  ? null : $message;
        $idM = $idMessage === 0 ? null : $idMessage;

        UserNotification::create([
            'user_id'    => $id,
            'read'       => 0,
            'file_id'    => $fileId,
            'message'    => $m,
            'message_id' => $idM,
            'status'     => 1,
        ]);
    }
}
