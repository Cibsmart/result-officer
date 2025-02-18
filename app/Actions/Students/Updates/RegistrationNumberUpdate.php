<?php

declare(strict_types=1);

namespace App\Actions\Students\Updates;

use App\Enums\ModifiableFields\StudentModifiableField;
use App\Models\DBMail;
use App\Models\Student;
use App\Models\StudentHistory;
use App\Values\RegistrationNumber;

final class RegistrationNumberUpdate
{
    public static function execute(
        Student $student,
        string $newRegistrationNumber,
        ?DBMail $dbMail = null,
    ): void {

        StudentHistory::createNewUpdate(
            student: $student,
            model: $student,
            updatedField: StudentModifiableField::REGISTRATION_NUMBER,
            data: ['new' => $newRegistrationNumber, 'old' => $student->registration_number],
            dbMail: $dbMail,
        );

        $student->updateRegistrationNumber(RegistrationNumber::new($newRegistrationNumber));
    }
}
