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
            $table->foreignId('company_id')->constrained('companies', 'id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trial_balance_data', function (Blueprint $table) {
            $table->dropColumn('company_id');
        });
    }
};
