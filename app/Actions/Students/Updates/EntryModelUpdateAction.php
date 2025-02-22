<?php

declare(strict_types=1);

namespace App\Actions\Students\Updates;

use App\Enums\EntryMode;
use App\Enums\ModifiableFields\StudentModifiableField;
use App\Models\Student;
use App\Models\StudentHistory;
use App\Models\User;

final class EntryModelUpdateAction
{
    public function execute(
        Student $student,
        EntryMode $newValue,
        string $remark = '',
        ?User $user = null,
    ): void {
        $oldValue = $student->entry_mode;

        $student->updateEntryMode($newValue);

        StudentHistory::createNewUpdate(
            student: $student,
            model: $student,
            updatedField: StudentModifiableField::ENTRY_MODE,
            data: ['new' => $newValue->value, 'old' => $oldValue->value],
            remark: $remark,
            user: $user,
        );
    }
}
