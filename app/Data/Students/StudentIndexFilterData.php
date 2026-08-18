<?php

declare(strict_types=1);

namespace App\Data\Students;

use App\Enums\Gender;
use App\Enums\SortDirection;
use App\Enums\StudentSortField;
use App\Enums\StudentStatus;
use Illuminate\Http\Request;
use Spatie\LaravelData\Data;

/**
 * The state of the student list page's search box, filters and column sort.
 *
 * Every field is nullable-or-defaulted rather than validated, because a filter
 * arriving from a hand-edited URL should narrow nothing rather than 422 the page.
 */
final class StudentIndexFilterData extends Data
{
    public function __construct(
        public readonly string $search,
        public readonly ?Gender $gender,
        public readonly ?StudentStatus $status,
        public readonly ?int $department,
        public readonly ?int $entrySession,
        public readonly StudentSortField $sort,
        public readonly SortDirection $direction,
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            search: $request->string('search')->trim()->value(),
            gender: Gender::tryFrom($request->string('gender')->value()),
            status: StudentStatus::tryFrom($request->string('status')->value()),
            department: self::positiveIntegerOrNull($request->integer('department')),
            entrySession: self::positiveIntegerOrNull($request->integer('entrySession')),
            sort: StudentSortField::fromValue($request->string('sort')->value()),
            direction: SortDirection::fromValue($request->string('direction')->value()),
        );
    }

    public function hasFilters(): bool
    {
        return $this->search !== ''
            || $this->gender instanceof Gender
            || $this->status instanceof StudentStatus
            || $this->department !== null
            || $this->entrySession !== null;
    }

    /** The dropdowns submit "0" for their placeholder option, which means "no filter". */
    private static function positiveIntegerOrNull(int $value): ?int
    {
        return $value > 0
            ? $value
            : null;
    }
}
