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
            $table->integer('month_balance')->isNotEmpty();
            $table->integer('current_balance')->isNotEmpty();
            $table->integer('previous_balance')->isNotEmpty();
            $table->integer('absolute_variation')->isNotEmpty();
            $table->integer('percetage_variation')->isNotEmpty();
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
};
