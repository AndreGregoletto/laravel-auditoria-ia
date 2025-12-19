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
        Schema::table('import_files', function (Blueprint $table) {
            $table->renameColumn('file_step', 'file_step_id');
            $table->renameColumn('status', 'file_status_id');
        });

        Schema::table('import_files', function (Blueprint $table) {
            $table->unsignedBigInteger('file_step_id')->change();
            $table->foreign('file_step_id')->references('id')->on('file_steps');

            $table->unsignedBigInteger('file_status_id')->change();
            $table->foreign('file_status_id')->references('id')->on('file_statuses');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('import_files', function (Blueprint $table) {
            $table->dropForeign(['file_step_id']);
            $table->dropForeign(['file_status_id']);
        });

        // Depois renomeamos de volta
        Schema::table('import_files', function (Blueprint $table) {
            $table->renameColumn('file_step_id', 'file_step');
            $table->renameColumn('file_status_id', 'status');
        });

        Schema::table('import_files', function (Blueprint $table) {
            $table->integer('file_step')->default(0)->change();
            $table->boolean('status')->default(1)->change();
        });
    }
};
