<?php

declare(strict_types=1);

namespace App\Actions\Results;

use App\Enums\ModifiableFields\StudentModifiableField;
use App\Enums\RecordActionType;
use App\Models\DBMail;
use App\Models\Registration;
use App\Models\Student;
use App\Models\StudentHistory;
use App\Models\User;

final class ResultDeleteAction
{
    /** @throws \Exception */
    public function execute(
        Student $student,
        Registration $registration,
        string $remark = '',
        ?DBMail $dbMail = null,
        ?User $user = null,
    ): void {
        $oldResultData = $registration->getUpdateData();
        $model = $registration;

        Registration::deleteRegistration($student, $registration);

        StudentHistory::createNewUpdate(
            student: $student,
            model: $model,
            updatedField: StudentModifiableField::RESULT,
            data: ['old' => $oldResultData],
            remark: $remark,
            dbMail: $dbMail,
            user: $user,
            action: RecordActionType::DELETE,
        );
    }
}
