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
            $table->boolean('balance_included')->nullable()->after('closing_balance');

            $table->foreignId('balance_last_decision_id')
                ->nullable()
                ->after('balance_included')
                ->constrained('trial_balance_decisions')
                ->nullOnDelete();

            $table->string('balance_decision_source', 20)
                ->nullable()
                ->after('balance_last_decision_id');

            $table->foreignId('decided_user_id')
                ->nullable()
                ->after('balance_decision_source')
                ->constrained('users', 'id')
                ->nullOnDelete();

            $table->timestamp('balance_decided_at')
                ->nullable()
                ->after('decided_user_id');

            $table->index(['file_id', 'company_id']);
            $table->index(['balance_included', 'file_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trial_balance_data', function (Blueprint $table) {
            $table->dropIndex(['file_id', 'company_id']);
            $table->dropIndex(['balance_included', 'file_id']);

            $table->dropConstrainedForeignId('balance_last_decision_id');
            $table->dropConstrainedForeignId('decided_user_id');

            $table->dropColumn([
                'balance_included',
                'balance_decision_source',
                'balance_decided_at',
            ]);
        });
    }
};
