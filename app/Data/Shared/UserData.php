<?php

declare(strict_types=1);

namespace App\Data\Shared;

use App\Models\User;
use Spatie\LaravelData\Data;

final class UserData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly bool $isAdmin,
        public readonly string $avatar,
        public readonly bool $emailVerified,
    ) {
    }

    public static function fromModel(User $user): self
    {
        return new self(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            isAdmin: $user->isAdmin(),
            avatar: '',
            emailVerified: ! is_null($user->email_verified_at),
        );
    }
}
