<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('trial_balance_data', function (Blueprint $table) {
            $table->foreignId('balance_sheet_id')
                ->nullable()
                ->constrained('balance_sheets');

            $table->foreignId('income_statement_id')
                ->nullable()
                ->constrained('income_statements');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trial_balance_data', function (Blueprint $table) {
            $table->dropColumn('balance_sheet_id');
            $table->dropColumn('income_statement_id');
        });
    }
};
