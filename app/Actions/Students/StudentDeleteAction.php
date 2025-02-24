<?php

declare(strict_types=1);

namespace App\Actions\Students;

use App\Enums\ModifiableFields\StudentModifiableField;
use App\Enums\RecordActionType;
use App\Models\DBMail;
use App\Models\Student;
use App\Models\StudentHistory;
use App\Models\User;

final class StudentDeleteAction
{
    /** @throws \Exception */
    public function execute(
        Student $student,
        string $remark = '',
        ?DBMail $dbMail = null,
        ?User $user = null,
    ): void {
        $oldValue = $student->registration_number;
        $model = $student;

        Student::deleteRegistration($student);

        StudentHistory::createNewUpdate(
            student: $student,
            model: $model,
            updatedField: StudentModifiableField::REGISTRATION_NUMBER,
            data: ['old' => $oldValue],
            remark: $remark,
            dbMail: $dbMail,
            user: $user,
            action: RecordActionType::DELETE,
        );
    }
}
