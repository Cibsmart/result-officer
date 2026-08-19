<?php

declare(strict_types=1);

namespace App\Services\Search;

use App\Data\Search\SearchGroupData;
use App\Data\Search\SearchResultData;
use App\Enums\SearchGroup;
use App\Models\Course;
use App\Models\Curriculum;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Program;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * Cross-model search behind the command palette and the /search page.
 *
 * Matching runs through Scout's database engine, so every column named by a
 * model's toSearchableArray() is matched with a LIKE against the table itself
 * — there is no index to keep in sync.
 */
final class GlobalSearch
{
    public const int MINIMUM_QUERY_LENGTH = 2;

    /**
     * Groups with no hit are dropped, so the palette never renders an empty heading.
     * @return array<int, \App\Data\Search\SearchGroupData>
     */
    public function handle(
        User $user,
        string $query,
        int $perGroup = 5,
    ): array {
        if (mb_strlen($query) < self::MINIMUM_QUERY_LENGTH) {
            return [];
        }

        $groups = array_map(
            fn (SearchGroup $group): SearchGroupData => SearchGroupData::fromGroup(
                $group,
                $this->resultsFor($group, $query, $perGroup),
            ),
            SearchGroup::visibleToUser($user),
        );

        return array_values(
            array_filter($groups, static fn (SearchGroupData $group): bool => $group->results !== []),
        );
    }

    /**
     * Neither programs nor curricula have a page of their own, so both land on
     * the program curricula table pre-filtered by Filament's own table search.
     */
    private static function programCurriculaUrl(string $tableSearch): string
    {
        return route(
            'filament.admin.resources.program-curriculums.index',
            ['tableSearch' => $tableSearch],
            absolute: false,
        );
    }

    /** @return array<int, \App\Data\Search\SearchResultData> */
    private function resultsFor(
        SearchGroup $group,
        string $query,
        int $perGroup,
    ): array {
        return match ($group) {
            SearchGroup::STUDENTS => $this->students($query, $perGroup),
            SearchGroup::COURSES => $this->courses($query, $perGroup),
            SearchGroup::CURRICULA => $this->curricula($query, $perGroup),
            SearchGroup::PROGRAMS => $this->programs($query, $perGroup),
            SearchGroup::DEPARTMENTS => $this->departments($query, $perGroup),
            SearchGroup::FACULTIES => $this->faculties($query, $perGroup),
        };
    }

    /** @return array<int, \App\Data\Search\SearchResultData> */
    private function students(string $query, int $perGroup): array
    {
        return Student::search($query)
            ->query(static fn (Builder $builder): Builder => $builder->with('program.department'))
            ->take($perGroup)
            ->get()
            ->map(static fn (Student $student): SearchResultData => SearchResultData::make(
                id: $student->id,
                title: "{$student->name}",
                url: route('students.show', ['student' => $student], absolute: false),
                subtitle: "{$student->registration_number} · {$student->program->department->name}",
                badge: $student->status->value,
            ))
            ->all();
    }

    /** @return array<int, \App\Data\Search\SearchResultData> */
    private function courses(string $query, int $perGroup): array
    {
        return Course::search($query)
            ->take($perGroup)
            ->get()
            ->map(static fn (Course $course): SearchResultData => SearchResultData::make(
                id: $course->id,
                title: $course->code,
                url: route('filament.admin.resources.courses.edit', ['record' => $course], absolute: false),
                subtitle: $course->title,
                external: true,
                badge: $course->active ? null : 'inactive',
            ))
            ->all();
    }

    /** @return array<int, \App\Data\Search\SearchResultData> */
    private function curricula(string $query, int $perGroup): array
    {
        return Curriculum::search($query)
            ->take($perGroup)
            ->get()
            ->map(static fn (Curriculum $curriculum): SearchResultData => SearchResultData::make(
                id: $curriculum->id,
                title: $curriculum->code,
                url: self::programCurriculaUrl($curriculum->code),
                subtitle: $curriculum->name,
                external: true,
            ))
            ->all();
    }

    /** @return array<int, \App\Data\Search\SearchResultData> */
    private function programs(string $query, int $perGroup): array
    {
        return Program::search($query)
            ->query(static fn (Builder $builder): Builder => $builder->with('department'))
            ->take($perGroup)
            ->get()
            ->map(static fn (Program $program): SearchResultData => SearchResultData::make(
                id: $program->id,
                title: $program->name,
                url: self::programCurriculaUrl($program->name),
                subtitle: "{$program->code} · {$program->department->name}",
                external: true,
                badge: $program->is_active ? null : 'inactive',
            ))
            ->all();
    }

    /** @return array<int, \App\Data\Search\SearchResultData> */
    private function departments(string $query, int $perGroup): array
    {
        return Department::search($query)
            ->query(static fn (Builder $builder): Builder => $builder->with('faculty'))
            ->take($perGroup)
            ->get()
            ->map(static fn (Department $department): SearchResultData => SearchResultData::make(
                id: $department->id,
                title: $department->name,
                url: route(
                    'filament.admin.resources.departments.edit',
                    ['record' => $department],
                    absolute: false,
                ),
                subtitle: "{$department->code} · {$department->faculty->name}",
                external: true,
                badge: $department->is_active ? null : 'inactive',
            ))
            ->all();
    }

    /** @return array<int, \App\Data\Search\SearchResultData> */
    private function faculties(string $query, int $perGroup): array
    {
        return Faculty::search($query)
            ->take($perGroup)
            ->get()
            ->map(static fn (Faculty $faculty): SearchResultData => SearchResultData::make(
                id: $faculty->id,
                title: $faculty->name,
                url: route('filament.admin.resources.faculties.edit', ['record' => $faculty], absolute: false),
                subtitle: $faculty->code,
                external: true,
                badge: $faculty->is_active ? null : 'inactive',
            ))
            ->all();
    }
}
