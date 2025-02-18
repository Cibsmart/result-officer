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

        $oldStudentName = [
            'first_name' => $student->first_name,
            'last_name' => $student->last_name,
            'other_names' => $student->other_names,
        ];

        $newStudentName = [...$oldStudentName];

        $newName = array_map(fn ($value) => Str::of($value)->trim()->upper()->value(), $newName);

        foreach ($newName as $key => $value) {
            $newStudentName[$key] = $value;
        }

        StudentHistory::createNewUpdate(
            student: $student,
            model: $student,
            updatedField: StudentModifiableField::NAME,
            data: ['new' => $this->joinName($newStudentName), 'old' => $student->name],
            remark: $remark,
            dbMail: $dbMail,
            user: $user,
        );

        $student->updateNames($newName);
    }

    /** @param array{last_name: string, first_name: string, other_names: string} $name */
    private function joinName(array $name): string
    {
        return $name['last_name'] . ' ' . $name['first_name'] . ' ' . $name['other_names'];
    }
}
