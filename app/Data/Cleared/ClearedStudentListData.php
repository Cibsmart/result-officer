<?php

declare(strict_types=1);

namespace App\Data\Cleared;

use App\Enums\StudentStatus;
use App\Models\Department;
use App\Models\StatusChangeEvent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

final class ClearedStudentListData extends Data
{
    public function __construct(
        /** @var \Illuminate\Support\Collection<int, \App\Data\Cleared\ClearedStudentData> */
        public readonly Collection $data,
    ) {
    }

    public static function fromModel(Department $department, int $year): self
    {
        $students = $department->students()
            ->whereHas('statusChangeEvents', function (Builder $query) use ($year): void {
                $query->where('status', StudentStatus::CLEARED)
                    ->whereYear('date', $year);
            })
            ->where('students.status', 'cleared')
            ->orderByDesc(StatusChangeEvent::query()->select('date')
                ->where('status', StudentStatus::CLEARED)
                ->whereColumn('students.id', 'student_id')
                ->latest()
                ->take(1),
            )
            ->get();

        return new self(data: ClearedStudentData::collect($students));
    }
}
