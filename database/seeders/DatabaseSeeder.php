<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'André Gregoletto',
            'email' => 'argregoletto@email.com',
            'password' => 'padrao123',
        ]);

        $this->call([
            FileStatus::class,
            TypeFiles::class,
            FileStep::class,
            Company::class,
            TreeCompany::class,
        ]);
    }
}
