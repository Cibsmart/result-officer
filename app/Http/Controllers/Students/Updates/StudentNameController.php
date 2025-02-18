<?php

declare(strict_types=1);

namespace App\Http\Controllers\Students\Updates;

use App\Actions\Students\Updates\StudentNameUpdate;
use App\Models\DBMail;
use App\Models\Student;
use App\Models\User;
use App\Values\DateValue;
use Illuminate\Http\RedirectResponse;

final class StudentNameController
{
    public function __invoke(
        Student $student,
        StudentNameRequest $request,
        StudentNameUpdate $action,
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

        $changedNameFields = $this->getChangedNamedFields($student, $validated);

        $action->execute($student, $changedNameFields, $validated['remark'], $dbMail);

        return redirect()
            ->route('students.show', $student)
            ->success('Student name updated successfully.');
    }

    /**
     * @param array<string, string|null> $validated
     * @return array{last_name?: string, first_name?: string, other_name?: string}
     */
    private function getChangedNamedFields(Student $student, array $validated): array
    {
        $nameFields = ['last_name', 'first_name', 'other_names'];
        $changedNameField = [];

        foreach ($nameFields as $nameField) {
            $newValue = $validated[$nameField];

            $newValue = $newValue === null ? '' : $newValue;

            if ($student->{$nameField} === $newValue) {
                continue;
            }

            $changedNameField[$nameField] = $newValue;
        }

        return $changedNameField;
    }
}
