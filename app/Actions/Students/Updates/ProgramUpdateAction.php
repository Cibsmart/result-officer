<?php

declare(strict_types=1);

namespace App\Actions\Students\Updates;

use App\Enums\ModifiableFields\StudentModifiableField;
use App\Enums\StudentRelatedField;
use App\Models\DBMail;
use App\Models\Program;
use App\Models\Student;
use App\Models\StudentHistory;
use App\Models\User;

final class ProgramUpdateAction
{
    public function execute(
        Student $student,
        Program $newValue,
        string $remark = '',
        ?DBMail $dbMail = null,
        ?User $user = null,
    ): void {
        $oldValue = $student->program;

        $student->updateRelatedField(StudentRelatedField::PROGRAM, $newValue->id);

        StudentHistory::createNewUpdate(
            student: $student,
            model: $student,
            updatedField: StudentModifiableField::PROGRAM,
            data: ['new' => $newValue->name, 'old' => $oldValue->name],
            remark: $remark,
            dbMail: $dbMail,
            user: $user,
        );
    }
}
