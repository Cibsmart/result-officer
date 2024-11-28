<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\StudentStatus;
use Illuminate\Database\Eloquent\Model;

final class StatusChangeEvent extends Model
{
    public static function recordStudentStatusChange(Student $student, StudentStatus $status): void
    {
        $statusChangeEvent = new self();

        $statusChangeEvent->student_id = $student->id;
        $statusChangeEvent->status = $status;
        $statusChangeEvent->date = now();

        $statusChangeEvent->save();
    }

    /** @return array{date: 'datetime', status: 'App\Enums\StudentStatus'} */
    protected function casts(): array
    {
        return [
            'date' => 'datetime',
            'status' => StudentStatus::class,
        ];
    }
}
