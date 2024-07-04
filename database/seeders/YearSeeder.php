<?php

namespace Database\Seeders;

use App\Models\Year;
use Illuminate\Database\Seeder;

class YearSeeder extends Seeder
{
    /** @var array<int, string> */
    private array $years = [
        'FIRST', 'SECOND', 'THIRD', 'FOURTH', 'FIFTH', 'SIXTH', 'SEVENTH', 'EIGHT',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->years as $year) {
            Year::query()->create(['name' => $year]);
        }
    }
}
