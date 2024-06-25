<?php

namespace Database\Seeders;

use App\Models\Faculty;
use Illuminate\Database\Seeder;

class FacultySeeder extends Seeder
{
    /**
     * @var array<string, string>
     */
    private array $faculties = [
        'FPS' => 'PHYSICAL SCIENCES',
        'FBS' => 'BIOLOGICAL SCIENCES',
        'FEDU' => 'EDUCATION',
        'FMS' => 'MANAGEMENT SCIENCES',
        'FSS' => 'SOCIAL SCIENCES',
        'FBMS' => 'BASIC MEDICAL SCIENCES',
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
    }
}
