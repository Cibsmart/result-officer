<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Faculty;
use Illuminate\Database\Seeder;

final class FacultySeeder extends Seeder
{
    /** @var array<string, string> */
    private array $faculties = [
        'FBMS' => 'BASIC MEDICAL SCIENCES',
        'FBS' => 'BIOLOGICAL SCIENCES',
        'FEDU' => 'EDUCATION',
        'FMS' => 'MANAGEMENT SCIENCES',
        'FPS' => 'PHYSICAL SCIENCES',
        'FSS' => 'SOCIAL SCIENCES',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->faculties as $code => $faculty) {
            Faculty::query()
                ->create([
                    'code' => $code,
                    'name' => $faculty,
                ]);
        }

        $this->call([
            DepartmentSeeder::class,
            ProgramSeeder::class,
        ]);
    }

}
