<?php

namespace App\Models\Queue;

use Illuminate\Database\Eloquent\Model;

class TrialBalanceData extends Model
{
    protected $fillable = [
        'file_id',
        'file_line',
        'account', //connta
        'description', //descrição
        'month_balance', // Saldo mes
        'current_balance',  // SAldo atual
        'previous_balance', // Saldo anterio
        'absolute_variation', // variação absoluta
        'percentage_variation', // Variação %
        'red_flag',
        'status'
    ];
}
