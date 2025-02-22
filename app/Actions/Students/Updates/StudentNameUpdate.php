<?php

declare(strict_types=1);

namespace App\Actions\Students\Updates;

use App\Enums\ModifiableFields\StudentModifiableField;
use App\Models\DBMail;
use App\Models\Student;
use App\Models\StudentHistory;
use App\Models\User;
use Illuminate\Support\Str;

final class StudentNameUpdate
{
    /** @param array{last_name?: string, first_name?: string, other_names?: string} $newName */
    public function execute(
        Student $student,
        array $newName,
        string $remark = '',
        ?DBMail $dbMail = null,
        ?User $user = null,
    ): void {
        $newName = array_map(fn ($value) => Str::of($value)->trim()->upper()->value(), $newName);

        $oldName = $student->name;

        $student->updateNames($newName);

        StudentHistory::createNewUpdate(
            student: $student,
            model: $student,
            updatedField: StudentModifiableField::NAME,
            data: ['new' => $student->fresh()->name, 'old' => $oldName],
            remark: $remark,
            dbMail: $dbMail,
            user: $user,
        );
    }
}
