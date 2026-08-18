<?php

declare(strict_types=1);

namespace App\Data\Students;

use App\Data\Dropdown\DropdownData;
use App\Enums\Gender;
use App\Enums\StudentStatus;
use App\Models\Department;
use App\Models\Session;
use App\Models\User;
use Spatie\LaravelData\Data;

/**
 * The option lists behind the student list page's filter bar. Each list leads with
 * an "All ..." entry whose empty value clears that filter.
 */
final class StudentFilterOptionsData extends Data
{
    public function __construct(
        /** @var array<int, \App\Data\Dropdown\DropdownData> */
        public readonly array $genders,
        /** @var array<int, \App\Data\Dropdown\DropdownData> */
        public readonly array $statuses,
        /** @var array<int, \App\Data\Dropdown\DropdownData> */
        public readonly array $departments,
        /** @var array<int, \App\Data\Dropdown\DropdownData> */
        public readonly array $years,
    ) {
    }

    public static function forUser(User $user): self
    {
        return new self(
            genders: self::withAllOption('All Genders', array_map(
                static fn (Gender $gender): DropdownData => new DropdownData(
                    value: $gender->value,
                    label: $gender->getLabel(),
                ),
                Gender::cases(),
            )),
            statuses: self::withAllOption('All Statuses', array_map(
                static fn (StudentStatus $status): DropdownData => new DropdownData(
                    value: $status->value,
                    label: str_replace('_', ' ', $status->name),
                ),
                StudentStatus::cases(),
            )),
            departments: self::withAllOption('All Departments', self::departments($user)),
            years: self::withAllOption('All Years', self::years()),
        );
    }

    /**
     * Scoped the same way every other department dropdown in the app is, so a desk
     * officer filters within the departments they are assigned to.
     * @return array<int, \App\Data\Dropdown\DropdownData>
     */
    private static function departments(User $user): array
    {
        $departmentIds = $user->departments()->pluck('department_id');

        return Department::query()
            ->when(! $user->isAdmin(), static fn ($query) => $query->whereIn('id', $departmentIds))
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(static fn (Department $department): DropdownData => new DropdownData(
                value: (string) $department->id,
                label: $department->name,
            ))
            ->all();
    }

    /**
     * Entry "year" is the first year of the entry session, so 2019/2020 shows as 2019.
     * The value stays the session id — that is what students are actually keyed on.
     * @return array<int, \App\Data\Dropdown\DropdownData>
     */
    private static function years(): array
    {
        return Session::query()
            ->orderBy('name', 'desc')
            ->get()
            ->map(static fn (Session $session): DropdownData => new DropdownData(
                value: (string) $session->id,
                label: (string) $session->firstYear(),
            ))
            ->all();
    }

    /**
     * @param array<int, \App\Data\Dropdown\DropdownData> $options
     * @return array<int, \App\Data\Dropdown\DropdownData>
     */
    private static function withAllOption(string $label, array $options): array
    {
        return array_merge([new DropdownData(value: '', label: $label)], $options);
    }
}
