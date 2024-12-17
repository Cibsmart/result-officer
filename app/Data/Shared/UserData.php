<?php

declare(strict_types=1);

namespace App\Data\Shared;

use App\Models\User;
use Spatie\LaravelData\Data;

final class UserData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public bool $isAdmin,
    ) {
    }

    public static function fromModel(User $user): self
    {
        return new self(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            isAdmin: $user->isAdmin(),
        );
    }
}
