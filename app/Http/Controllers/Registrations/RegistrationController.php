<?php

declare(strict_types=1);

namespace App\Http\Controllers\Registrations;

use App\Models\DBMail;
use App\Models\Registration;
use App\Models\Student;
use App\Values\DateValue;
use Illuminate\Http\RedirectResponse;

final class RegistrationController
{
    public function destroy(
        Student $student,
        Registration $registration,
        RegistrationDeleteRequest $request,
    ): RedirectResponse {
        $validated = $request->validated();

        $user = $request->user();

        $dbMail = DBMail::createFromRecordUpdate(
            user: $user,
            mailTitle: $validated['mail_title'],
            mailDate: DateValue::fromValue($validated['mail_date']),
        );

        $course = $registration->course;

        return redirect()
            ->route('students.show', ['student' => $student])
            ->success("Result ({$course->code} - {$course->title}) deleted successfully.");
    }
}
