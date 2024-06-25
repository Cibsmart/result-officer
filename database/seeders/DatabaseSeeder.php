<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::query()->create([
            'name' => 'Admin User',
            'email' => 'admin@admin.com',
            'password' => 'password',
            'role' => 'admin',
        ]);

        $this->call([
            FacultySeeder::class,
            DepartmentSeeder::class,
            ProgramSeeder::class,
            SessionSeeder::class,
            SemesterSeeder::class,
        ]);
    }
}
