<?php

declare(strict_types=1);

namespace App\Actions\Students\Updates;

use App\Enums\ModifiableFields\StudentModifiableField;
use App\Enums\StudentRelatedField;
use App\Models\LocalGovernment;
use App\Models\Student;
use App\Models\StudentHistory;
use App\Models\User;

final class LocalGovernmentUpdateAction
{
    public function execute(
        Student $student,
        LocalGovernment $newValue,
        string $remark = '',
        ?User $user = null,
    ): void {
        $oldValue = $student->lga;

        $student->updateRelatedField(StudentRelatedField::LOCAL_GOVERNMENT, $newValue->id);

        $student->fresh();

        StudentHistory::createNewUpdate(
            student: $student,
            model: $student,
            updatedField: StudentModifiableField::LOCAL_GOVERNMENT,
            data: ['new' => $newValue->name, 'old' => $oldValue->name],
            remark: $remark,
            user: $user,
        );
    }
}
