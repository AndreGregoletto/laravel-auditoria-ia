<?php

namespace App\Models\Queue;

use Illuminate\Database\Eloquent\Model;

class TrialBalanceData extends Model
{
    protected $fillable = [
        'file_id',
        'company_id',
        'file_line',
        'account',          // conta
        'description',      // descrição
        'previous_balance', // Saldo anterio
        'debit',            // Débito
        'credit',           // Crédito
        'monthly_activity', // Mov Mensal / Periodo
        'closing_balance',  // Saldo final / Atual
        'red_flag',
        'status',
    ];
}
