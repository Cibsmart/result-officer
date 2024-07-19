<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ProgramType;
use Illuminate\Database\Seeder;

final class ProgramTypeSeeder extends Seeder
{
    /** @var array<string, string> */
    private array $programTypes = [
        'B.A.' => 'BACHELOR OF ART',
        'B.Eng.' => 'BACHELOR OF ENGINEERING',
        'B.L.' => 'BACHELOR OF LAW',
        'B.NSc.' => 'BACHELOR OF NURSING SCIENCE',
        'B.Sc.' => 'BACHELOR OF SCIENCE',
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
