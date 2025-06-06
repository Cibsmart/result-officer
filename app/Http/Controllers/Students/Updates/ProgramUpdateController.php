<?php

declare(strict_types=1);

namespace App\Http\Controllers\Students\Updates;

use App\Actions\Students\Updates\ProgramUpdateAction;
use App\Models\DBMail;
use App\Models\Program;
use App\Models\Student;
use App\Models\User;
use App\Values\DateValue;
use Illuminate\Http\RedirectResponse;

final class ProgramUpdateController
{
    public function __invoke(
        Student $student,
        ProgramUpdateRequest $request,
        ProgramUpdateAction $action,
    ): RedirectResponse {
        $validated = $request->validated();

        $user = $request->user();
        assert($user instanceof User);

        $dbMail = null;

        if ($validated['has_mail']) {
            $dbMail = DBMail::createFromRecordUpdate(
                user: $user,
                mailTitle: $validated['mail_title'],
                mailDate: DateValue::fromValue($validated['mail_date']),
            );
        }

        $newValue = Program::getUsingId($validated['program']);

        $action->execute($student, $newValue, $validated['remark'], $dbMail, $user);

        return redirect()
            ->route('students.show', ['student' => $student])
            ->success('Student program updated successfully.');
    }
}
