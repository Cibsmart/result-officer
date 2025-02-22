<?php

declare(strict_types=1);

namespace App\Http\Controllers\Students\Updates;

use App\Actions\Students\Updates\BirthDateUpdateAction;
use App\Models\Student;
use App\Models\User;
use App\Values\DateValue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class BirthDateUpdateController
{
    public function __invoke(
        Student $student,
        Request $request,
        BirthDateUpdateAction $action,
    ): RedirectResponse {
        $validated = $request->validate(['date_of_birth' => ['required']]);

        $user = $request->user();
        assert($user instanceof User);

        $newValue = DateValue::fromValue($validated['date_of_birth']);

        $action->execute($student, $newValue, $validated['remark'], $user);

        return redirect()
            ->route('students.show', ['student' => $student])
            ->success('Student date of birth updated successfully.');
    }
}
