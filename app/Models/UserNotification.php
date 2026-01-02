<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{

    protected $fillable = [
        'user_id',
        'read',
        'file_id',
//        'file_download_id', TODO create pivot table to download files generate to system
        'message',
        'message_id',
        'status',
    ];

    protected $casts = [
        'created_at',
        'updated_at',
        'status' => 'boolean',
    ];

    public function file()
    {
        return $this->hasOne(ImportFile::class , 'id', 'file_id');
    }

    public function message()
    {
        return $this->hasOne(Message::class , 'id', 'message_id');
    }
}
