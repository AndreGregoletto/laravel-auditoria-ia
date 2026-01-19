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
        \App\Models\FileStatus::insert([
            [
                'name'      => 'Inactive',
                'name_conf' => 'inactive',
                'status'    => 1,
            ], [
                'name'      => 'Active',
                'name_conf' => 'active',
                'status'    => 1,
            ], [
                'name'      => 'File generated',
                'name_conf' => 'file_generated',
                'status'    => 1,
            ],
        ]);
    }
}
