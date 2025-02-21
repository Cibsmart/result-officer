<?php

declare(strict_types=1);

namespace App\Actions\Results;

use App\Enums\ModifiableFields\StudentModifiableField;
use App\Models\DBMail;
use App\Models\Registration;
use App\Models\Student;
use App\Models\StudentHistory;
use App\Models\User;

final class ResultUpdateAction
{
    /** @param array{credit_unit?: int, in_course?: int, exam?: int} $newResult */
    public function execute(
        Student $student,
        Registration $registration,
        array $newResult,
        string $remark = '',
        ?DBMail $dbMail = null,
        ?User $user = null,
    ): void {
        $oldResultData = $registration->getUpdateData();

        Registration::updateRegistrationAndResult($student, $registration, $newResult);

        $registration->fresh();

        StudentHistory::createNewUpdate(
            student: $student,
            model: $registration,
            updatedField: StudentModifiableField::RESULT,
            data: ['new' => $registration->getUpdateData(), 'old' => $oldResultData],
            remark: $remark,
            dbMail: $dbMail,
            user: $user,
        );
    }
}
