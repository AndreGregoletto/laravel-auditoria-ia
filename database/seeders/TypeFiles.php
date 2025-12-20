<?php

namespace Database\Seeders;

use App\Models\TypeFile;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TypeFiles extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TypeFile::insert(
            ['name' => 'balance', 'status' => 1],
            ['name' => 'rag', 'status' => 1],
        );
    }
}
