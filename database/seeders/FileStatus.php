<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FileStatus extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\FileStatus::create(
            [
                'name'      => 'Processing',
                'name_conf' => 'processing',
            ], [
                'name'      => 'Processed',
                'name_conf' => 'processed',
            ], [
                'name'      => 'Error',
                'name_conf' => 'error',
            ], [
                'name'      => 'Cancelled',
                'name_conf' => 'cancelled',
            ], [
                'name'      => 'In Queue',
                'name_conf' => 'in_queue',
            ], [
                'name'      => 'RAG generated',
                'name_conf' => 'rag_generated',
            ],
        );
    }
}
