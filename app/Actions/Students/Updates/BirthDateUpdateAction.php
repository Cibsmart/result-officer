<?php

declare(strict_types=1);

namespace App\Actions\Students\Updates;

use App\Enums\ModifiableFields\StudentModifiableField;
use App\Models\Student;
use App\Models\StudentHistory;
use App\Models\User;
use App\Values\DateValue;

final class BirthDateUpdateAction
{
    public function execute(
        Student $student,
        DateValue $newValue,
        string $remark = '',
        ?User $user = null,
    ): void {
        $birthDate = $student->date_of_birth;

        $oldValue = $birthDate
            ? $birthDate->format('Y-m-d')
            : '';

        $student->updateBirthDate($newValue);

        StudentHistory::createNewUpdate(
            student: $student,
            model: $student,
            updatedField: StudentModifiableField::DATE_OF_BIRTH,
            data: ['new' => $newValue->toString('Y-m-d'), 'old' => $oldValue],
            remark: $remark,
            user: $user,
        );
    }
}
