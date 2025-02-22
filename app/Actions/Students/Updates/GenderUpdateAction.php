<?php

declare(strict_types=1);

namespace App\Actions\Students\Updates;

use App\Enums\Gender;
use App\Enums\ModifiableFields\StudentModifiableField;
use App\Models\Student;
use App\Models\StudentHistory;
use App\Models\User;

final class GenderUpdateAction
{
    public function execute(
        Student $student,
        Gender $newValue,
        string $remark = '',
        ?User $user = null,
    ): void {
        $oldValue = $student->gender;

        $student->updateGender($newValue);

        StudentHistory::createNewUpdate(
            student: $student,
            model: $student,
            updatedField: StudentModifiableField::GENDER,
            data: ['new' => $newValue->value, 'old' => $oldValue->value],
            remark: $remark,
            user: $user,
        );
    }
}
