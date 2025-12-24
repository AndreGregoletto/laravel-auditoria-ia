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
        Schema::table('company_trees', function (Blueprint $table) {
            $table->renameColumn('company_parent_id', 'company_tree_id');
            $table->renameColumn('company_parent', 'holding');
            $table->foreignId('company_parent_id')->constrained('companies', 'id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_trees', function (Blueprint $table) {
            $table->dropColumn('company_parent_id');
            $table->renameColumn('company_tree_id', 'company_parent_id');
            $table->renameColumn('holding', 'company_parent');
        });
    }
};
