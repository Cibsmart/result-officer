<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\GenderEnum;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

final class StudentSeeder extends Seeder
{
    private array $students = [
        [
            'date_of_birth' => '2004-9-10',
            'entry_level_id' => 1,
            'entry_mode_id' => 1,
            'entry_session_id' => 1,
            'first_name' => 'STUDENT',
            'gender' => GenderEnum::MALE,
            'last_name' => 'FIRST',
            'other_names' => null,
            'program_id' => 1,
            'registration_number' => 'EBSU/2009/51485',
            'state_id' => 1,
        ],
        [
            'date_of_birth' => '1988-7-27',
            'entry_level_id' => 1,
            'entry_mode_id' => 1,
            'entry_session_id' => 1,
            'first_name' => 'BARNABAS',
            'gender' => GenderEnum::MALE,
            'last_name' => 'IFEBUDE',
            'other_names' => 'CHUKWUDIKE',
            'program_id' => 1,
            'registration_number' => 'EBSU/2009/51486',
            'state_id' => 1,
        ],
        [
            'date_of_birth' => '1987-8-19',
            'entry_level_id' => 1,
            'entry_mode_id' => 1,
            'entry_session_id' => 1,
            'first_name' => 'RUTH',
            'gender' => GenderEnum::FEMALE,
            'last_name' => 'NWEKE',
            'other_names' => 'OGECHUKWU',
            'program_id' => 1,
            'registration_number' => 'EBSU/2009/51895',
            'state_id' => 1,
        ],
        [
            'date_of_birth' => '1990-8-19',
            'entry_level_id' => 1,
            'entry_mode_id' => 1,
            'entry_session_id' => 1,
            'first_name' => 'STUDENT',
            'gender' => GenderEnum::FEMALE,
            'last_name' => 'EMPTY',
            'other_names' => null,
            'program_id' => 1,
            'registration_number' => 'EBSU/2009/51896',
            'state_id' => 1,
        ],
    ];

    public function run(): void
    {
        foreach ($this->students as $student) {
            Student::query()->create([
                'date_of_birth' => Carbon::make($student['date_of_birth']),
                'entry_level_id' => $student['entry_level_id'],
                'entry_mode_id' => $student['entry_mode_id'],
                'entry_session_id' => $student['entry_session_id'],
                'first_name' => $student['first_name'],
                'gender' => $student['gender'],
                'last_name' => $student['last_name'],
                'other_names' => $student['other_names'],
                'program_id' => $student['program_id'],
                'registration_number' => $student['registration_number'],
                'state_id' => $student['state_id'],
            ]);
        }
    }
}
