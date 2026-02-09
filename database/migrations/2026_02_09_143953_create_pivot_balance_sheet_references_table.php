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
        Schema::create('pivot_balance_sheet_references', function (Blueprint $table) {
            $table->id();
            $table->foreignId('balance_sheet_id')
                ->nullable()
                ->constrained('balance_sheets');
            $table->string('value', 50)->nullable();
            $table->foreignId('company_tree_id')
                ->nullable()
                ->constrained('company_trees');
            $table->foreignId('company_id')
                ->nullable()
                ->constrained('companies');
            $table->boolean('status')->default(1);
            $table->foreignId('create_user_id')->constrained('users');
            $table->foreignId('alter_user_id')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pivot_balance_sheet_references');
    }
};
