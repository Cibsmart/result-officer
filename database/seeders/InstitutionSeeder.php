<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Tests\Factories\InstitutionFactory;

final class InstitutionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        InstitutionFactory::new()
            ->state([
                'address' => 'ABAKALIKI',
                'code' => 'EBSU',
                'name' => 'EBONYI STATE UNIVERSITY',
                'strategy' => 'semester',
            ])
            ->createOne();
    }
}
