<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Faculty;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * @var array<string, string>
     */
    private array $departments = [
        'CSCI' => 'COMPUTER SCIENCE/INFORMATICS',
        'MTHSTA' => 'MATHEMATICS/STATICS',
        'CHEM' => 'CHEMISTRY',
        'PHYGEOP' => 'PHYSIC/APPLIED GEOPHYSICS',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->departments as $code => $name) {
            Department::query()->create([
                'faculty_id' => Faculty::query()->where('code', 'FPS')->first()->id,
                'code' => $code,
                'name' => $name,
            ]);
        }
    }
}
