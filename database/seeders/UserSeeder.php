<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

final class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->create([
            'email' => 'super@ebsu.edu.ng',
            'name' => 'Super Admin',
            'password' => 'password',
            'role' => Role::SUPER_ADMIN,
        ]);
    }
}
