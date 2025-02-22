<?php

declare(strict_types=1);

namespace App\Actions\Students\Updates;

use App\Enums\ModifiableFields\StudentModifiableField;
use App\Enums\StudentField;
use App\Models\Student;
use App\Models\StudentHistory;
use App\Models\User;

final class StudentPhoneNumberUpdateAction
{
    public function execute(
        Student $student,
        string $newValue,
        string $remark = '',
        ?User $user = null,
    ): void {
        $oldValue = $student->phone_number;

        $student->updateField(StudentField::PHONE_NUMBER, $newValue);

        StudentHistory::createNewUpdate(
            student: $student,
            model: $student,
            updatedField: StudentModifiableField::PHONE_NUMBER,
            data: ['new' => $newValue, 'old' => $oldValue ? $oldValue : ''],
            remark: $remark,
            user: $user,
        );
    }
}
