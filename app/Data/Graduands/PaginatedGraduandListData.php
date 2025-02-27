<?php

declare(strict_types=1);

namespace App\Data\Graduands;

use App\Enums\StudentStatus;
use App\Models\Department;
use Illuminate\Pagination\AbstractPaginator;
use Spatie\LaravelData\Data;

final class PaginatedGraduandListData extends Data
{
    public function __construct(
        /** @var \Illuminate\Pagination\AbstractPaginator<\App\Data\Graduands\GraduandData> $paginated */
        public readonly AbstractPaginator $paginated,
    ) {
    }

    public static function for(Department $department): self
    {
        $graduands = $department->students()
            ->with('vettingEvent')
            ->whereIn('status', StudentStatus::vettableStates())
            ->orderBy('registration_number')
            ->paginate();

        $paginated = GraduandData::collect($graduands);
        assert($paginated instanceof AbstractPaginator);

        return new self(paginated: $paginated);
    }
}
