<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\GenderEnum;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

final class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Student::query()->create([
            'country_id' => 1,
            'date_of_birth' => Carbon::create(2004, 9, 1),
            'entry_level_id' => 1,
            'entry_mode_id' => 1,
            'entry_session_id' => 1,
            'first_name' => 'STUDENT',
            'gender' => GenderEnum::MALE,
            'last_name' => 'NEW',
            'matriculation_number' => 'EBSU/2023/54563',
            'program_id' => 1,
        ]);
    }
}
