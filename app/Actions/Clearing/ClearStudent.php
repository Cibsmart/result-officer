<?php

declare(strict_types=1);

namespace App\Actions\Clearing;

use App\Enums\StudentStatus;
use App\Models\StatusChangeEvent;
use App\Models\Student;
use Exception;

final class ClearStudent
{
    /** @throws \Exception */
    public function execute(Student $student): void
    {
        if (! in_array($student->status, StudentStatus::clearableStates(), true)) {
            throw new Exception('Student has not been vetted and cannot be cleared');
        }

        StatusChangeEvent::recordStudentStatusChange($student, StudentStatus::CLEARED);

        $student->updateStatus(StudentStatus::CLEARED);
    }
}
