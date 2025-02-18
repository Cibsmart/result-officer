<?php

declare(strict_types=1);

namespace App\Http\Controllers\Students\Updates;

use App\Actions\Students\Updates\RegistrationNumberUpdate;
use App\Models\DBMail;
use App\Models\Student;
use App\Models\User;
use App\Values\DateValue;
use Illuminate\Http\RedirectResponse;

final class RegistrationNumberController
{
    public function __invoke(
        Student $student,
        RegistrationNumberRequest $request,
        RegistrationNumberUpdate $action,
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

        $action->execute($student, $validated['registration_number'], $dbMail);

        return redirect()
            ->route('students.show', ['student' => $student])
            ->success('Registration number updated.');
    }
}
