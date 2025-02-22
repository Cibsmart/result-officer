<?php

declare(strict_types=1);

namespace App\Actions\Students\Updates;

use App\Enums\ModifiableFields\StudentModifiableField;
use App\Models\DBMail;
use App\Models\Student;
use App\Models\StudentHistory;
use App\Models\User;
use App\Values\RegistrationNumber;

final class RegistrationNumberUpdate
{
    public function execute(
        Student $student,
        RegistrationNumber $newRegistrationNumber,
        string $remark = '',
        ?DBMail $dbMail = null,
        ?User $user = null,
    ): void {
        $oldRegistrationNumber = $student->registration_number;

        $student->updateRegistrationNumber($newRegistrationNumber);

        StudentHistory::createNewUpdate(
            student: $student,
            model: $student,
            updatedField: StudentModifiableField::REGISTRATION_NUMBER,
            data: ['new' => $newRegistrationNumber->value, 'old' => $oldRegistrationNumber],
            remark: $remark,
            dbMail: $dbMail,
            user: $user,
        );
    }
}
