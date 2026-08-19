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
    /**
     * @param array{credit_unit?: int, in_course?: int, exam?: int} $newResult
     * @throws \Exception
     */
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

        // No reload before reading the new value: the update path writes through
        // $registration and its already-loaded result relation, so this instance
        // is current. A discarded $registration->fresh() call sat here, which
        // read as a stale-audit bug but was only ever dead code.
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
