<?php

declare(strict_types=1);

namespace App\Actions\Students\Updates;

use App\Enums\ModifiableFields\StudentModifiableField;
use App\Enums\StudentStatus;
use App\Models\DBMail;
use App\Models\Student;
use App\Models\StudentHistory;
use App\Models\User;

final class StudentStatusUpdate
{
    public static function execute(
        Student $student,
        StudentStatus $newStatus,
        string $remark = '',
        ?DBMail $dbMail = null,
        ?User $user = null,
    ): void {

        $oldStatus = $student->status->value;

        $student->updateStatus($newStatus);

        StudentHistory::createNewUpdate(
            student: $student,
            model: $student,
            updatedField: StudentModifiableField::STATUS,
            data: ['new' => $newStatus->value, 'old' => $oldStatus],
            remark: $remark,
            dbMail: $dbMail,
            user: $user,
        );
    }
}
