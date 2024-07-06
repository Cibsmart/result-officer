<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\RoleEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

final class User extends Authenticatable
{
    use Notifiable;

    /** @var array<int, string> */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /** @var array<int, string> */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => RoleEnum::class,
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Casts\Attribute<string, string>
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            set: static fn (string $value): string => strtoupper($value),
        );
    }
}
