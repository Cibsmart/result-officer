<?php

declare(strict_types=1);

namespace App\Data\Shared;

use Spatie\LaravelData\Data;

final class UserData extends Data
{
    public function __construct(public string $name, public string $email)
    {
    }
}
