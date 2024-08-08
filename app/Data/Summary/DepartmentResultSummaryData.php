<?php

declare(strict_types=1);

namespace App\Data\Summary;

use App\Data\Department\DepartmentData;
use App\Data\Level\LevelData;
use App\Data\Session\SessionData;
use App\Models\Department;
use App\Models\Level;
use App\Models\Session;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

final class DepartmentResultSummaryData extends Data
{
    public function __construct(
        public readonly DepartmentData $department,
        public readonly SessionData $session,
        public readonly LevelData $level,
        /** @var \Illuminate\Support\Collection<int, \App\Data\Summary\StudentResultSummaryData> */
        public readonly Collection $students,
    ) {
    }

    public static function fromModel(
        Department $department,
        Session $session,
        Level $level,
    ): self {
        return new self(
            department: DepartmentData::from($department),
            session: SessionData::from($session),
            level: LevelData::from($level),
            students: StudentResultSummaryData::collect(
                $department
                    ->students()
                    ->whereHas('enrollments',
                        static function (Builder $query) use ($session, $level): void {
                            $query->where('session_id', $session->id)
                                ->where('level_id', $level->id);
                        })
                    ->get(),
            )
                ->sortByDesc('fcgpa')
                ->values(),
        );
    }
}
