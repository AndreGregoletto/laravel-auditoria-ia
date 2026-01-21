<?php

namespace App\AI\Intent;

enum IntentType: string
{
    case SYSTEM_METRIC = 'system_metric';
    case COMPANY_TREE  = 'company_tree';
    case IMPORT_QUEUE  = 'import_queue';
    case TRIAL_BALANCE = 'trial_balance';
    case RAG           = 'rag';
    case AUDIT_CONCEPT = 'audit_concept';
    case UNKNOWN       = 'unknown';
}
