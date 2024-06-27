<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\LocalGovernment;
use Illuminate\Database\Seeder;

final class LocalGovernmentSeeder extends Seeder
{

    /** @var array<int, string> */
    private array $lgas = [
        'ABA NORTH',
        'ABA SOUTH',
        'AROCHUKWU',
        'BENDE',
        'IKWUANO',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->lgas as $lga) {
            LocalGovernment::query()->create([
                'name' => $lga,
                'state_id' => 1,
            ]);
        }
    }

}
