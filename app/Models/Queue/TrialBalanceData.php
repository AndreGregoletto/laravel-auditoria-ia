<?php

namespace App\Models\Queue;

use App\Models\ImportFile;
use App\Models\TrialBalanceDecision;
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
        'balance_sheet_id',
        'income_statement_id',

//        Snapshot
//        'balance_included',
//        'balance_last_decision_id',
//        'balance_decision_source',
//        'decided_user_id',
//        'balance_decided_at'
    ];

    public function decisions()
    {
        return $this->hasMany(TrialBalanceDecision::class, 'trial_balance_data_id');
    }

    public function lastDecision()
    {
        return $this->belongsTo(TrialBalanceDecision::class, 'balance_last_decision_id');
    }

    public function decidedUser()
    {
        return $this->belongsTo(\App\Models\User::class, 'decided_user_id');
    }

    public function file()
    {
        return $this->hasOne(ImportFile::class, 'id', 'file_id');
    }

}
