<?php

declare(strict_types=1);

namespace App\Http\Controllers\Students\Updates;

use App\Actions\Students\Updates\LocalGovernmentUpdateAction;
use App\Models\LocalGovernment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class LocalGovernmentUpdateController
{
    public function __invoke(
        Student $student,
        Request $request,
        LocalGovernmentUpdateAction $action,
    ): RedirectResponse {
        $validated = $request->validate([
            'local_government' => ['required', 'integer', 'exists:local_governments,id'],
            'remark' => ['required', 'string', 'min:3', 'max:255'],
            'state' => ['required', 'integer', 'exists:states,id'],
        ]);

        $user = $request->user();
        assert($user instanceof User);

        $newValue = LocalGovernment::getUsingId($validated['local_government']);

        $action->execute($student, $newValue, $validated['remark'], $user);

        return redirect()
            ->route('students.show', ['student' => $student])
            ->success('Student local government updated successfully.');
    }
}
