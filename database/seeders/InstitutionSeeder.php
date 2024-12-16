<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Institution;
use Illuminate\Database\Seeder;

final class InstitutionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Institution::query()
            ->create([
                'address' => 'ABAKALIKI',
                'code' => 'EBSU',
                'domain' => 'ebsu.edu.ng',
                'name' => 'EBONYI STATE UNIVERSITY',
                'strategy' => 'semester',
            ]);
    }
}
