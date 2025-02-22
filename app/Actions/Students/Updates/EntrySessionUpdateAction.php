<?php

declare(strict_types=1);

namespace App\Actions\Students\Updates;

use App\Enums\ModifiableFields\StudentModifiableField;
use App\Enums\StudentRelatedField;
use App\Models\Session;
use App\Models\Student;
use App\Models\StudentHistory;
use App\Models\User;

final class EntrySessionUpdateAction
{
    public function execute(
        Student $student,
        Session $newValue,
        string $remark = '',
        ?User $user = null,
    ): void {
        $oldValue = $student->entrySession;

        $student->updateRelatedField(StudentRelatedField::ENTRY_SESSION, $newValue->id);

        StudentHistory::createNewUpdate(
            student: $student,
            model: $student,
            updatedField: StudentModifiableField::ENTRY_SESSION,
            data: ['new' => $newValue->name, 'old' => $oldValue->name],
            remark: $remark,
            user: $user,
        );
    }
}
