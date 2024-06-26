<?php

declare(strict_types = 1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

final class DatabaseSeeder extends Seeder
{

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::query()->create([
            'email' => 'admin@admin.com',
            'name' => 'Admin User',
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
