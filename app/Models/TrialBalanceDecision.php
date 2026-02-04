<?php

namespace App\Models;

use App\Models\Queue\TrialBalanceData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrialBalanceDecision extends Model
{
    protected $fillable = [
        'trial_balance_data_id', // Referência à linha original do balancete
        'file_id',
        'company_id',
        'balance_included', //  Indica se a conta foi incluída ou excluída
        'source', // Origem da decisão.
        'reason', //Justificativa da decisão
        'ai_confidence', // Grau de confiança da IA (0–100),
        'ai_model', //  Identificação do modelo de IA utilizado
        'ai_rationale', // Justificativa textual gerada pela IA explicando
        'batch_id', //Identificador de lote (UUID)/ Usado para agrupar decisões realizadas em massa
        'decided_user_id', // Usuário (auditor) que tomou ou aprovou a decisão.
        'decided_at', //Data e hora em que a decisão foi efetivamente tomada.
        'balance_sheet_id',
        'income_statement_id',
        'decision_type', // Incluido || Classificacao_bp || Classificacao_dre || Classificacao_both
    ];

    protected $casts = [
        'balance_included' => 'boolean',
        'ai_confidence'    => 'integer',
        'decided_at'       => 'datetime',
    ];

    public function trialBalanceData()
    {
        return $this->belongsTo(\App\Models\Queue\TrialBalanceData::class, 'trial_balance_data_id');
    }

    public function file()
    {
        return $this->belongsTo(\App\Models\ImportFile::class, 'file_id');
    }

    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class, 'company_id');
    }

    public function decidedUser()
    {
        return $this->belongsTo(\App\Models\User::class, 'decided_user_id');
    }
}
