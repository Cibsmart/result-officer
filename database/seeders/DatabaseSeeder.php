<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ProgramTypeSeeder::class,
            SessionSeeder::class,
            SemesterSeeder::class,
            CountrySeeder::class,
            LevelSeeder::class,
            CourseTypeSeeder::class,
            CurriculumSeeder::class,
            RecordsUnitHeadSeeder::class,
        ]);
    }
}
