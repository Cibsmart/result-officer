<?php

declare(strict_types=1);

namespace App\Actions\Students\Updates;

use App\Enums\ModifiableFields\StudentModifiableField;
use App\Enums\StudentRelatedField;
use App\Models\Level;
use App\Models\Student;
use App\Models\StudentHistory;
use App\Models\User;

final class EntryLevelUpdateAction
{
    public function execute(
        Student $student,
        Level $newValue,
        string $remark = '',
        ?User $user = null,
    ): void {
        $oldValue = $student->entryLevel;

        $student->updateRelatedField(StudentRelatedField::ENTRY_LEVEL, $newValue->id);

        StudentHistory::createNewUpdate(
            student: $student,
            model: $student,
            updatedField: StudentModifiableField::ENTRY_LEVEL,
            data: ['new' => $newValue->name, 'old' => $oldValue->name],
            remark: $remark,
            user: $user,
        );
    }
}
