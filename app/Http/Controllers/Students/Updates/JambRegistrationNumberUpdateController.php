<?php

declare(strict_types=1);

namespace App\Http\Controllers\Students\Updates;

use App\Actions\Students\Updates\JambRegistrationNumberUpdateAction;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class JambRegistrationNumberUpdateController
{
    public function __invoke(
        Student $student,
        Request $request,
        JambRegistrationNumberUpdateAction $action,
    ): RedirectResponse {
        $validated = $request->validate(['jamb_registration_number' => ['required']]);

        $user = $request->user();
        assert($user instanceof User);

        $newValue = $validated['jamb_registration_number'];

        $action->execute($student, $newValue, $validated['remark'], $user);

        return redirect()
            ->route('students.show', ['student' => $student])
            ->success('Student JAMB registration number updated successfully.');
    }
}
