<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Level;
use Illuminate\Database\Seeder;

final class LevelSeeder extends Seeder
{
    /** @var array<int, string> */
    private array $levels = [
        '100', '200', '300', '400', '500', '600', 'EXTRA YEAR',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->levels as $level) {
            Level::query()->create([
                'name' => $level,
            ]);
        }
    }
}
