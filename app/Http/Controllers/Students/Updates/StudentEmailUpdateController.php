<?php

declare(strict_types=1);

namespace App\Http\Controllers\Students\Updates;

use App\Actions\Students\Updates\StudentEmailUpdateAction;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class StudentEmailUpdateController
{
    public function __invoke(
        Student $student,
        Request $request,
        StudentEmailUpdateAction $action,
    ): RedirectResponse {
        $validated = $request->validate(['email' => ['required']]);

        $user = $request->user();
        assert($user instanceof User);

        $newValue = $validated['email'];

        $action->execute($student, $newValue, $validated['remark'], $user);

        return redirect()
            ->route('students.show', ['student' => $student])
            ->success('Student email updated successfully.');
    }
}
