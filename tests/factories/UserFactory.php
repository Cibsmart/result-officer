<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Enums\Role;
use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User> */
final class UserFactory extends Factory
{
    protected $model = User::class;
    protected static ?string $password;

    /**
     * Emails default to the institution's domain. Permissions — like isAdmin()
     * before them — only apply to in-domain accounts, and every account is now
     * provisioned by a super admin rather than self-registered.
     * @return array<string, string>
     */
    public function definition(): array
    {
        return [
            'email' => fake()->unique()->userName() . '@' . config('rp.domain'),
            'email_verified_at' => now(),
            'name' => fake()->name(),
            'password' => self::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => 'user',
        ];
    }

    public function role(Role $role): self
    {
        // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter
        return $this->state(fn (array $attributes): array => ['role' => $role->value]);
    }

    public function superAdmin(): self
    {
        return $this->role(Role::SUPER_ADMIN);
    }

    public function admin(): self
    {
        return $this->role(Role::ADMIN);
    }

    public function databaseOfficer(): self
    {
        return $this->role(Role::DATABASE_OFFICER);
    }

    public function examOfficer(): self
    {
        return $this->role(Role::EXAM_OFFICER);
    }

    public function deskOfficer(): self
    {
        return $this->role(Role::DESK_OFFICER);
    }

    /** Assign the user to a department, as UserDepartment does in production. */
    public function forDepartment(Department $department): self
    {
        return $this->afterCreating(static function (User $user) use ($department): void {
            UserDepartmentFactory::new()->createOne([
                'department_id' => $department->id,
                'user_id' => $user->id,
            ]);
        });
    }

    public function unverified(): self
    {
        // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
