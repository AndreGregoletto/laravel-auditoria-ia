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
            $table->string('parent_code', 10)->nullable();
            $table->integer('sort_order')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('income_statements', function (Blueprint $table) {
            $table->dropColumn('parent_code');
            $table->dropColumn('sort_order');
        });
    }
};
