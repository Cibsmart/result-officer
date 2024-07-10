<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\GenderEnum;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

final class StudentSeeder extends Seeder
{
    public function run(): void
    {
        Student::query()->create([
            'country_id' => 1,
            'date_of_birth' => Carbon::create(2004, 9, 10),
            'entry_level_id' => 1,
            'entry_mode_id' => 1,
            'entry_session_id' => 15,
            'first_name' => 'STUDENT',
            'gender' => GenderEnum::MALE,
            'last_name' => 'NEW',
            'matriculation_number' => 'EBSU/2023/54563',
            'program_id' => 1,
        ]);

        Student::query()->create([
            'country_id' => 1,
            'date_of_birth' => Carbon::create(1988, 7, 27),
            'entry_level_id' => 1,
            'entry_mode_id' => 1,
            'entry_session_id' => 1,
            'first_name' => 'BARNABAS',
            'other_names' => 'CHUKWUDIKE',
            'gender' => GenderEnum::MALE,
            'last_name' => 'IFEBUDE',
            'matriculation_number' => 'EBSU/2009/51486',
            'program_id' => 1,
        ]);

        Student::query()->create([
            'country_id' => 1,
            'date_of_birth' => Carbon::create(1987, 8, 19),
            'entry_level_id' => 1,
            'entry_mode_id' => 1,
            'entry_session_id' => 1,
            'first_name' => 'RUTH',
            'other_names' => 'OGECHUKWU',
            'gender' => GenderEnum::FEMALE,
            'last_name' => 'NWEKE',
            'matriculation_number' => 'EBSU/2009/51895',
            'program_id' => 1,
        ]);
    }
}
