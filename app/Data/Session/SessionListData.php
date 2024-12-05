<?php

declare(strict_types=1);

namespace App\Data\Session;

use App\Models\Session;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

final class SessionListData extends Data
{
    public function __construct(
        /** @var \Illuminate\Support\Collection<int, \App\Data\Session\SessionData> */
        public readonly Collection $sessions,
    ) {
    }

    public static function new(): self
    {
        $default = new SessionData(id: 0, name: 'Select Session', slug: '');

        return new self(
            sessions: SessionData::collect(
                Session::query()->orderBy('name', 'desc')->get(),
            )->prepend($default),
        );
    }
}
