<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Faculty;
use App\Models\Program;
use App\Models\ProgramType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class FacultySeeder extends Seeder
{
    public function run(): void
    {
        $content = Storage::get('seeders/programs.csv');

        assert(! is_null($content));

        $lines = explode("\n", $content);

        $header = [];

        $currentFaculty = '';
        $currentDepartment = '';

        $faculty = null;
        $department = null;

        foreach ($lines as $index => $line) {
            /** @var array<int, string> $data */
            $data = str_getcsv($line);

            if ($index === 0) {
                $header = collect($data)->map(fn ($value) => Str::slug($value, '_'))->all();

                continue;
            }

            /** @var array<string, string> $item */
            $item = array_combine($header, $data);

            if ($currentFaculty !== $item['faculty_name']) {

                $currentFaculty = $item['faculty_name'];
                $code = $item['faculty_code'];

                $faculty = Faculty::query()->firstOrCreate([
                    'code' => $code,
                    'name' => $currentFaculty,
                ]);

            }

            if ($currentDepartment !== $item['department_name']) {
                $currentDepartment = $item['department_name'];
                $code = $item['department_code'];
                $onlineId = $item['department_online_id'];

                $department = Department::query()->firstOrCreate([
                    'code' => $code,
                    'faculty_id' => $faculty->id,
                    'name' => $currentDepartment,
                    'online_id' => $onlineId,
                ]);

            }

            Program::query()->create([
                'code' => $item['program_code'],
                'department_id' => $department->id,
                'name' => $item['program_name'],
                'program_type_id' => ProgramType::getUsingCode($item['program_type'])->id,
            ]);

        }
    }
}
