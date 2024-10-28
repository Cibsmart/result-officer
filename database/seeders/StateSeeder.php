<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\State;
use Illuminate\Database\Seeder;

final class StateSeeder extends Seeder
{
    /** @var array<int, string> */
    private array $states = [
        'ABIA', 'ADAMAWA', 'AKWA IBOM', 'ANAMBRA', 'BAUCHI', 'BAYELSA', 'BENUE', 'BORNU', 'CROSS RIVER', 'DELTA',
        'EBONYI', 'EDO', 'EKITI', 'ENUGU', 'FCT', 'GOMBE', 'IMO', 'JIGAWA', 'KADUNA', 'KANO', 'KASTINA', 'KEBBI',
        'KOGI', 'KWARA', 'LAGOS', 'NASSARAWA', 'NIGER', 'OGUN', 'ONDO', 'OSUN', 'OYO', 'PLATEAU', 'RIVERS', 'SOKOTO',
        'TARABA', 'YOBE', 'ZAMFARA', 'OTHERS',
    ];

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
