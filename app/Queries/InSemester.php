<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Semester;
use Illuminate\Database\Eloquent\Builder;

final readonly class InSemester
{
    public function __construct(private Semester $semester)
    {
    }

    public function __invoke(Builder $query): void
    {
        $query->where('semester_id', $this->semester->id);
    }
}
