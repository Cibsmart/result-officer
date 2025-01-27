<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Models\UserDepartment;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserDepartment> */
final class UserDepartmentFactory extends Factory
{
    protected $model = UserDepartment::class;

    /** @return array<string, string> */
    public function definition(): array
    {
        return [
            'department_id' => DepartmentFactory::new(),
            'user_id' => UserFactory::new(),
        ];
    }
}
