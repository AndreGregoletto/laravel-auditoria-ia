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
        Schema::table('income_statements', function (Blueprint $table) {
            $table->foreignId('company_tree_id')->nullable()->change();
            $table->foreignId('company_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('income_statements', function (Blueprint $table) {
            $table->foreignId('company_tree_id')->nullable(false)->change();
            $table->foreignId('company_id')->nullable(false)->change();
        });
    }
};
