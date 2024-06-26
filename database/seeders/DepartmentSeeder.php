<?php

declare(strict_types = 1);

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Faculty;
use Illuminate\Database\Seeder;

final class DepartmentSeeder extends Seeder
{

    /** @var array<string, string> */
    private array $departments = [
        'CHEM' => 'CHEMISTRY',
        'CSCI' => 'COMPUTER SCIENCE/INFORMATICS',
        'MTHSTA' => 'MATHEMATICS/STATICS',
        'PHYGEOP' => 'PHYSIC/APPLIED GEOPHYSICS',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->departments as $code => $name) {
            Department::query()->create([
                'code' => $code,
                'faculty_id' => Faculty::query()->where('code', 'FPS')->first()?->id,
                'name' => $name,
            ]);
        }
    }

}
