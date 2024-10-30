<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ProgramType;
use Illuminate\Database\Seeder;

final class ProgramTypeSeeder extends Seeder
{
    /** @var array<string, string> */
    private array $programTypes = [
        'B.A.' => 'BACHELOR OF ARTS',
        'B.A.Ed.' => 'BACHELOR OF ARTS EDUCATION',
        'B.Agric.' => 'BACHELOR OF AGRICULTURE',
        'B.Ed.' => 'BACHELOR OF EDUCATION',
        'B.Eng.' => 'BACHELOR OF ENGINEERING',
        'B.MLS' => 'BACHELOR OF MEDICAL LABORATORY SCIENCE',
        'B.N.Sc.' => 'BACHELOR OF NURSING SCIENCE',
        'B.Sc.' => 'BACHELOR OF SCIENCE',
        'B.Sc.Ed.' => 'BACHELOR OF SCIENCE EDUCATION',
        'LL.B' => 'BACHELOR OF LAW',
        'MBBS' => 'BACHELOR OF MEDICINE, BACHELOR OF SURGERY',

    ];

    public function run(): void
    {
        foreach ($this->programTypes as $code => $name) {
            ProgramType::query()->create([
                'code' => $code,
                'name' => $name,
            ]);
        }
    }
}
