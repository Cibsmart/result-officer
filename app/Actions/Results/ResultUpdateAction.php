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

        $result = $registration->result;
        $scores = json_decode($result->scores);

        $oldResultDetail = [
            'credit_unit' => $registration->credit_unit->value,
            'exam' => $scores->exam,
            'in_course' => $scores->in_course,
        ];

        $newResultDetail = [...$oldResultDetail];

        foreach ($newResult as $key => $value) {
            $newResultDetail[$key] = $value;
        }

        Registration::updateRegistrationAndResult($student, $registration, $newResult);

        StudentHistory::createNewUpdate(
            student: $student,
            model: $registration,
            updatedField: StudentModifiableField::RESULT,
            data: ['new' => $this->getResultText($newResultDetail), 'old' => $this->getResultText($oldResultDetail)],
            remark: $remark,
            dbMail: $dbMail,
            user: $user,
        );
    }

    /** @param array{credit_unit: int, in_course: int, exam: int} $result */
    public function getResultText(array $result): string
    {
        return "{$result['credit_unit']}-{$result['in_course']}-{$result['exam']}";
    }
}
