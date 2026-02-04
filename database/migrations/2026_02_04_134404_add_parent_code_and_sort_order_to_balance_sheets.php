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
        Schema::table('balance_sheets', function (Blueprint $table) {
            $table->string('parent_code', 10)->nullable();
            $table->integer('sort_order')->nullable();
            $table->string('side', 100)->nullable();
            $table->string('section', 100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('balance_sheets', function (Blueprint $table) {
            $table->dropColumn('parent_code');
            $table->dropColumn('sort_order');
            $table->dropColumn('side');
            $table->dropColumn('section');
        });
    }
};
