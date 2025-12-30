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
        'status',
    ];

}
