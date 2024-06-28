<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

final class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Country::query()->create([
            'code' => 'NG',
            'demonym' => 'NIGERIAN',
            'name' => 'NIGERIA',
        ]);

        Country::query()->create([
            'code' => 'IT',
            'demonym' => 'INTERNATIONAL',
            'name' => 'FORIEGNER',
        ]);

        $this->call([
            StateSeeder::class,
            LocalGovernmentSeeder::class,
        ]);
    }

}
