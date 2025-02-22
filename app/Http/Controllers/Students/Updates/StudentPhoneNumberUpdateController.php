<?php

declare(strict_types=1);

namespace App\Http\Controllers\Students\Updates;

use App\Actions\Students\Updates\StudentPhoneNumberUpdateAction;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class StudentPhoneNumberUpdateController
{
    public function __invoke(
        Student $student,
        Request $request,
        StudentPhoneNumberUpdateAction $action,
    ): RedirectResponse {
        $validated = $request->validate(['phone_number' => ['required']]);

        $user = $request->user();
        assert($user instanceof User);

        $newValue = $validated['phone_number'];

        $action->execute($student, $newValue, $validated['remark'], $user);

        return redirect()
            ->route('students.show', ['student' => $student])
            ->success('Student phone number updated successfully.');
    }
}
