<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\EntryMode;
use App\Enums\Gender;
use App\Models\Program;
use App\Models\Session;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

final class StudentSeeder extends Seeder
{
    private array $students = [
        [
            'date_of_birth' => '2004-9-10',
            'entry_level_id' => 1,
            'entry_mode' => EntryMode::UTME,
            'entry_session' => '2009/2010',
            'first_name' => 'STUDENT',
            'gender' => Gender::MALE,
            'last_name' => 'FIRST',
            'local_government_id' => 1,
            'other_names' => null,
            'registration_number' => 'EBSU/2009/51485',
        ],
        [
            'date_of_birth' => '1988-7-27',
            'entry_level_id' => 1,
            'entry_mode' => EntryMode::UTME,
            'entry_session' => '2009/2010',
            'first_name' => 'BARNABAS',
            'gender' => Gender::MALE,
            'last_name' => 'IFEBUDE',
            'local_government_id' => 1,
            'other_names' => 'CHUKWUDIKE',
            'registration_number' => 'EBSU/2009/51486',
        ],
        [
            'date_of_birth' => '1987-8-19',
            'entry_level_id' => 1,
            'entry_mode' => EntryMode::UTME,
            'entry_session' => '2009/2010',
            'first_name' => 'RUTH',
            'gender' => Gender::FEMALE,
            'last_name' => 'NWEKE',
            'local_government_id' => 1,
            'other_names' => 'OGECHUKWU',
            'registration_number' => 'EBSU/2009/51895',
        ],
        [
            'date_of_birth' => '1990-8-19',
            'entry_level_id' => 1,
            'entry_mode' => EntryMode::UTME,
            'entry_session' => '2009/2010',
            'first_name' => 'STUDENT',
            'gender' => Gender::FEMALE,
            'last_name' => 'EMPTY',
            'local_government_id' => 1,
            'other_names' => null,
            'registration_number' => 'EBSU/2009/51896',
        ],
    ];

    public function run(): void
    {
        $program = Program::query()->where('name', 'COMPUTER SCIENCE')->first();

        foreach ($this->students as $student) {
            $session = Session::getUsingName($student['entry_session']);
            Student::query()->create([
                'date_of_birth' => Carbon::make($student['date_of_birth']),
                'entry_level_id' => $student['entry_level_id'],
                'entry_mode' => $student['entry_mode'],
                'entry_session_id' => $session->id,
                'first_name' => $student['first_name'],
                'gender' => $student['gender'],
                'last_name' => $student['last_name'],
                'local_government_id' => $student['local_government_id'],
                'other_names' => $student['other_names'],
                'program_id' => $program->id,
                'registration_number' => $student['registration_number'],
                'slug' => Str::slug($student['registration_number']),
            ]);
        }
    }
}
