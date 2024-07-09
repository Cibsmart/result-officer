<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

final class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ProgramTypeSeeder::class,
            FacultySeeder::class,
            SessionSeeder::class,
            SemesterSeeder::class,
            CourseSeeder::class,
            CountrySeeder::class,
            EntryModeSeeder::class,
            LevelSeeder::class,
            CourseTypeSeeder::class,
            CurriculumSeeder::class,
            ProgramDurationSeeder::class,
            StudentStatusSeeder::class,
            StudentSeeder::class,
            CourseStatusSeeder::class,
            ScoreTypeSeeder::class,
            EnrollmentSeeder::class,
        ]);
    }
}
