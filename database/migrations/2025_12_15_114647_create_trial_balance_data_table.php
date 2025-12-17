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
        Schema::create('trial_balance_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('file_id')->constrained('import_files', 'id');
            $table->integer('file_line')->isNotEmpty();
            $table->string('account', 250)->isNotEmpty();
            $table->string('description', 250)->isNotEmpty();
            $table->integer('previous_balance')->isNotEmpty();
            $table->integer('debit')->isNotEmpty();
            $table->integer('credit')->isNotEmpty();
            $table->integer('monthly_activity')->isNotEmpty();
            $table->integer('closing_balance')->isNotEmpty();
            $table->integer('red_flag')->isNotEmpty();
            $table->boolean('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trial_balance_data');
    }
//    php artisan migrate:refresh --path=database/migrations/2025_12_15_114647_create_trial_balance_data_table.php
};
