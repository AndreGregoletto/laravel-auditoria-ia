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
            $table->string('decision_type', 50)
                ->nullable()
                ->after('balance_included');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trial_balance_decisions', function (Blueprint $table) {
            $table->dropColumn('decision_type');
        });
    }
};
