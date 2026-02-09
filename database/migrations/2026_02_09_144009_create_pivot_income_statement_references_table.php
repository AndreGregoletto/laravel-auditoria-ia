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
        Schema::create('pivot_income_statement_references', function (Blueprint $table) {
            $table->id();
            $table->foreignId('income_statement_id')
                ->nullable()
                ->constrained('income_statements');
            $table->string('value', 50);
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
        Schema::dropIfExists('pivot_income_statement_references');
    }
};
