<?php

declare(strict_types=1);

namespace App\Queries;

use App\Data\Students\StudentIndexFilterData;
use App\Enums\StudentSortField;
use App\Models\Student;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * The student list page's query: free-text search, four filters and a column sort,
 * all driven by the query string so any view of the list is a shareable URL.
 */
final readonly class StudentIndex
{
    private const int PER_PAGE = 15;

    public function __construct(private StudentIndexFilterData $filters)
    {
    }

    public static function new(StudentIndexFilterData $filters): self
    {
        return new self($filters);
    }

    /** @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<int, \App\Models\Student> */
    public function paginate(): LengthAwarePaginator
    {
        return $this->builder()
            ->paginate(self::PER_PAGE)
            ->withQueryString();
    }

    /** @return \Illuminate\Database\Eloquent\Builder<\App\Models\Student> */
    private function builder(): Builder
    {
        $builder = Student::query()->with('program.department.faculty', 'entrySession', 'lga.state.country');

        $this->applySearch($builder);
        $this->applyFilters($builder);
        $this->applySort($builder);

        return $builder;
    }

    /**
     * Each whitespace-separated token has to match somewhere, so "okonkwo chidinma"
     * finds the student whose surname and first name are split across two columns.
     * @param \Illuminate\Database\Eloquent\Builder<\App\Models\Student> $builder
     */
    private function applySearch(Builder $builder): void
    {
        if ($this->filters->search === '') {
            return;
        }

        foreach (Str::of($this->filters->search)->squish()->explode(' ') as $token) {
            $term = '%' . $token . '%';

            $builder->where(static function (Builder $query) use ($term): void {
                $query->where('registration_number', 'like', $term)
                    ->orWhere('last_name', 'like', $term)
                    ->orWhere('first_name', 'like', $term)
                    ->orWhere('other_names', 'like', $term);
            });
        }
    }

    /** @param \Illuminate\Database\Eloquent\Builder<\App\Models\Student> $builder */
    private function applyFilters(Builder $builder): void
    {
        $builder
            ->when($this->filters->gender, static fn (Builder $query, $gender) => $query->where('gender', $gender))
            ->when($this->filters->status, static fn (Builder $query, $status) => $query->where('status', $status))
            ->when(
                $this->filters->department,
                static fn (Builder $query, int $department) => $query->whereRelation(
                    'program',
                    'department_id',
                    $department,
                ),
            )
            ->when(
                $this->filters->entrySession,
                static fn (Builder $query, int $session) => $query->where('entry_session_id', $session),
            );
    }

    /**
     * `id` is always the final tie-break so that two students sharing a surname keep a
     * stable position between pages instead of drifting across page boundaries.
     * @param \Illuminate\Database\Eloquent\Builder<\App\Models\Student> $builder
     */
    private function applySort(Builder $builder): void
    {
        $direction = $this->filters->direction->value;

        if ($this->filters->sort === StudentSortField::DEPARTMENT) {
            $builder->orderBy($this->departmentNameSubquery(), $direction);
        }

        foreach ($this->filters->sort->columns() as $column) {
            $builder->orderBy($column, $direction);
        }

        $builder->orderBy('students.id');
    }

    /**
     * A student's department is two tables away (students → programs → departments), so
     * sorting on its name uses a correlated subquery rather than a join that would
     * otherwise have to be de-duplicated before pagination counts rows.
     */
    private function departmentNameSubquery(): QueryBuilder
    {
        return DB::table('departments')
            ->select('departments.name')
            ->join('programs', 'programs.department_id', '=', 'departments.id')
            ->whereColumn('programs.id', 'students.program_id')
            ->limit(1);
    }
}
