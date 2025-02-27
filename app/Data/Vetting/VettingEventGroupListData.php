<?php

declare(strict_types=1);

namespace App\Data\Vetting;

use App\Models\User;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

final class VettingEventGroupListData extends Data
{
    public function __construct(
        /** @var \Illuminate\Support\Collection<int, \App\Data\Vetting\VettingEventGroupData> $data */
        public readonly Collection $data,
    ) {
    }

    public static function forUser(User $user): self
    {
        $vettingGroups = $user->vettingEventGroups()
            ->with('department')
            ->latest()
            ->get();

        return new self(data: VettingEventGroupData::collect($vettingGroups));
    }
}
