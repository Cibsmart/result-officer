<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Helpers\CSVFile;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Program;
use App\Models\ProgramType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

final class FacultySeeder extends Seeder
{
    public function run(): void
    {
        /** @var \Illuminate\Support\Collection<int, \Illuminate\Support\Collection<string, string>> $content */
        $content = (new CSVFile('seeders/programs.csv'))->read();

        $faculties = $content->sortBy([
            ['faculty_name', 'asc'],
            ['department_name', 'asc'],
        ])->groupBy('faculty_name');

        foreach ($faculties as $facultyName => $departments) {
            $facultyCode = $departments->firstOrFail()['faculty_code'];

            $faculty = Faculty::query()->firstOrCreate([
                'code' => $facultyCode,
                'name' => $facultyName,
            ]);

            $this->createDepartmentsAndPrograms($faculty, $departments);
        }
    }

    /** @param \Illuminate\Support\Collection<int, \Illuminate\Support\Collection<string, string>> $departments */
    private function createDepartmentsAndPrograms(
        Faculty $faculty,
        Collection $departments,
    ): void {
        foreach ($departments->groupBy('department_name') as $departmentName => $programs) {
            $departmentCode = $programs->firstOrFail()['department_code'];
            $departmentOnlineId = $programs->firstOrFail()['department_online_id'];

            $department = Department::query()->firstOrCreate([
                'code' => $departmentCode,
                'faculty_id' => $faculty->id,
                'name' => $departmentName,
                'online_id' => $departmentOnlineId,
            ]);

            foreach ($programs as $program) {
                Program::query()->create([
                    'code' => $program['program_code'],
                    'department_id' => $department->id,
                    'name' => $program['program_name'],
                    'program_type_id' => ProgramType::getUsingCode($program['program_type'])->id,
                ]);
            }
        }
    }
}
