<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Program;
use App\Models\ProgramType;
use Illuminate\Database\Seeder;

final class ProgramSeeder extends Seeder
{
    /** @var array<string, array<string, string>> */
    private array $departmentPrograms = [
        'CSC' => [
            'CSC' => 'COMPUTER SCIENCE',
            'IFM' => 'INFORMATICS',
        ],
        'MTHSTA' => [
            'MTH' => 'MATHEMATICS',
            'STA' => 'STATICS',
        ],
    ];

    public function run(): void
    {
        foreach ($this->departmentPrograms as $department => $programs) {
            foreach ($programs as $code => $name) {
                Program::query()->create([
                    'code' => $code,
                    'department_id' => Department::query()->where('code', $department)->first()->id,
                    'name' => $name,
                    'program_type_id' => ProgramType::query()->where('code', 'B.Sc.')->first()->id,
                ]);
            }
        }
    }
}
