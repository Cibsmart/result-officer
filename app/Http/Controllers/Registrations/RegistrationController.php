<?php

declare(strict_types=1);

namespace App\Http\Controllers\Registrations;

use App\Actions\Results\ResultDeleteAction;
use App\Enums\NotificationType;
use App\Models\DBMail;
use App\Models\Registration;
use App\Models\Student;
use App\Values\DateValue;
use Exception;
use Illuminate\Http\RedirectResponse;

final class RegistrationController
{
    public function destroy(
        Student $student,
        Registration $registration,
        RegistrationDeleteRequest $request,
        ResultDeleteAction $action,
    ): RedirectResponse {
        $validated = $request->validated();

        $user = $request->user();

        $dbMail = DBMail::createFromRecordUpdate(
            user: $user,
            mailTitle: $validated['mail_title'],
            mailDate: DateValue::fromValue($validated['mail_date']),
        );

        $course = $registration->course;

        try {
            $action->execute(
                student: $student,
                registration: $registration,
                remark: $validated['remark'],
                dbMail: $dbMail,
                user: $user,
            );
        } catch (Exception $e) {
            return redirect()
                ->route('students.show', ['student' => $student])
                ->{NotificationType::ERROR->value}("{$e->getMessage()}");
        }

        return redirect()
            ->route('students.show', ['student' => $student])
            ->{NotificationType::SUCCESS->value}("Result ({$course->code} - {$course->title}) deleted successfully.");
    }
}
