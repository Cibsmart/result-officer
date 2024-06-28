<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\State;
use Illuminate\Database\Seeder;

final class StateSeeder extends Seeder
{
    /** @var array<int, string> */
    private array $states = [
        'ABIA', 'ANAMBRA', 'EBONYI', 'ENUGU', 'IMO',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->states as $state) {
            State::query()->create([
                'country_id' => 1,
                'name' => $state,
            ]);
        }
    }

}
