<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CourseType;
use Illuminate\Database\Seeder;

final class CourseTypeSeeder extends Seeder
{
    /** @var array<int, string> */
    private array $courseTypes = [
        'C' => 'COMPULSORY',
        'E' => 'ELECTIVE',
        'R' => 'REQUIRED',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->courseTypes as $code => $name) {
            CourseType::query()->create([
                'code' => $code,
                'name' => $name,
            ]);
        }
    }

}
