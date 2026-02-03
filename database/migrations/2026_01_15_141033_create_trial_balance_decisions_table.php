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
        Schema::create('trial_balance_decisions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('trial_balance_data_id')
                ->constrained('trial_balance_data', 'id')
                ->cascadeOnDelete();

            $table->foreignId('file_id')
                ->constrained('import_files', 'id')
                ->cascadeOnDelete();

            $table->foreignId('company_id')
                ->constrained('companies', 'id')
                ->cascadeOnDelete();

            $table->boolean('balance_included');

            $table->string('source', 20);

            $table->text('reason')->nullable();

            $table->unsignedTinyInteger('ai_confidence')->nullable();
            $table->string('ai_model', 50)->nullable();
            $table->text('ai_rationale')->nullable();

            $table->uuid('batch_id')->nullable();

            $table->foreignId('decided_user_id')
                ->nullable()
                ->constrained('users', 'id')
                ->nullOnDelete();

            $table->timestamp('decided_at')->useCurrent();

            $table->timestamps();

            $table->index(['file_id', 'company_id']);
            $table->index(['trial_balance_data_id', 'decided_at']);
            $table->index(['batch_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trial_balance_decisions');
    }
};
