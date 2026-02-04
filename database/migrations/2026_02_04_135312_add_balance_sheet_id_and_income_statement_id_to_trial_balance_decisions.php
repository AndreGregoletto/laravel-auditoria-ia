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
        Schema::table('trial_balance_decisions', function (Blueprint $table) {
            $table->dropForeign(['balance_sheet_id']);
            $table->dropForeign(['income_statement_id']);

            $table->foreign('balance_sheet_id')
                ->references('id')->on('balance_sheets')
                ->nullOnDelete();

            $table->foreign('income_statement_id')
                ->references('id')->on('income_statements')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trial_balance_decisions', function (Blueprint $table) {
            $table->dropForeign(['balance_sheet_id']);
            $table->dropForeign(['income_statement_id']);

            $table->foreign('balance_sheet_id')
                ->references('id')->on('balance_sheets');

            $table->foreign('income_statement_id')
                ->references('id')->on('income_statements');
        });
    }
};
