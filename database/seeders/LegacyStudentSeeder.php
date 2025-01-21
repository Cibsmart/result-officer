<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\RawDataStatus;
use App\Helpers\CSVFile;
use App\Models\LegacyStudent;
use Illuminate\Database\Seeder;

final class LegacyStudentSeeder extends Seeder
{
    public function run(): void
    {

        /** @var \Illuminate\Support\Collection<int, \Illuminate\Support\Collection<string, string>> $content */
        $content = (new CSVFile('seeders/students_legacy.csv'))->read();

        $students = $content->sortBy('registration_number');

        foreach ($students as $student) {
            LegacyStudent::query()->create([
                'birth_date' => $student['birth_date'],
                'email' => $student['email'],
                'entry_level' => $student['entry_level'],
                'entry_mode' => $student['entry_mode'],
                'entry_year' => $student['entry_year'],
                'first_name' => $student['firstname'],
                'gender' => $student['gender'],
                'jamb_registration_number' => $student['jamb_registration_number'],
                'last_name' => $student['lastname'],
                'legacy_id' => $student['id'],
                'local_government' => $student['lga'],
                'other_names' => $student['othernames'],
                'phone_number' => $student['phone_number'],
                'process_status' => RawDataStatus::PENDING,
                'program_code' => $student['program_code'],
                'registration_number' => $student['registration_number'],
                'status' => $student['status'],
            ]);
        }

    }
}
