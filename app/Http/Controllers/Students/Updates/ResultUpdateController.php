<?php

declare(strict_types=1);

namespace App\Http\Controllers\Students\Updates;

use App\Actions\Results\ResultUpdateAction;
use App\Enums\NotificationType;
use App\Models\DBMail;
use App\Models\Registration;
use App\Models\Student;
use App\Models\User;
use App\Values\DateValue;
use Exception;
use Illuminate\Http\RedirectResponse;

final class ResultUpdateController
{
    public function __invoke(
        Student $student,
        ResultUpdateRequest $request,
        ResultUpdateAction $action,
    ): RedirectResponse {

        $user = $request->user();
        assert($user instanceof User);

        $dbMail = null;
        $validated = $request->validated();

        if ($request->validated('has_mail')) {
            $dbMail = DBMail::createFromRecordUpdate(
                user: $user,
                mailTitle: $validated['mail_title'],
                mailDate: DateValue::fromValue($validated['mail_date']),
            );
        }

        $registration = Registration::getUsingId($validated['registration_id']);

        $changedResultFields = $this->getChangedResultFields($registration, $validated);

        try {
            $action->execute($student, $registration, $changedResultFields, $validated['remark'], $dbMail);
        } catch (Exception $e) {
            return redirect()
                ->route('students.show', $student)
                ->{NotificationType::ERROR->value}($e->getMessage());
        }

        return redirect()
            ->route('students.show', $student)
            ->{NotificationType::SUCCESS->value}('Student name updated successfully.');
    }

    /**
     * @param array<string, int> $validated
     * @return array{credit_unit?: int, in_course?: int, exam?: int}
     */
    private function getChangedResultFields(Registration $registration, array $validated): array
    {
        $result = $registration->result;

        $oldResult = [
            'credit_unit' => $registration->credit_unit->value,
            'exam' => 0,
            'in_course' => 0,
            'in_course_2' => 0,
        ];

        if ($result) {
            $scores = $result->getScores();

            $oldResult['in_course'] = $scores['in_course'];
            $oldResult['in_course_2'] = $scores['in_course_2'];
            $oldResult['exam'] = $scores['exam'];
        }

        $changedResultField = [];

        foreach ($oldResult as $field => $value) {
            $newValue = $validated[$field];

            if ($value === $newValue) {
                continue;
            }

            $changedResultField[$field] = $newValue;
        }

        return $changedResultField;
    }
}
