<?php

declare(strict_types=1);

namespace App\Data\Vetting;

use App\Models\User;
use Illuminate\Pagination\AbstractPaginator;
use Spatie\LaravelData\Data;

final class VettingEventGroupListData extends Data
{
    public function __construct(
        /** @var \Illuminate\Pagination\AbstractPaginator<\App\Data\Vetting\VettingEventGroupData> $paginated */
        public readonly AbstractPaginator $paginated,
    ) {
    }

    public static function forUser(User $user): self
    {
        $vettingGroups = $user->vettingEventGroups()
            ->with('department')
            ->latest()
            ->paginate();

        assert($vettingGroups instanceof AbstractPaginator);

        return new self(paginated: VettingEventGroupData::collect($vettingGroups));
    }
}
