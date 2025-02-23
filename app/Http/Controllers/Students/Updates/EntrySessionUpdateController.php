<?php

declare(strict_types=1);

namespace App\Http\Controllers\Students\Updates;

use App\Actions\Students\Updates\EntrySessionUpdateAction;
use App\Models\Session;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class EntrySessionUpdateController
{
    public function __invoke(
        Student $student,
        Request $request,
        EntrySessionUpdateAction $action,
    ): RedirectResponse {
        $validated = $request->validate([
            'entry_session' => ['required', 'exists:academic_sessions,id'],
            'remark' => ['required', 'string'],
        ]);

        $user = $request->user();
        assert($user instanceof User);

        $newValue = Session::getUsingId($validated['entry_session']);

        $action->execute($student, $newValue, $validated['remark'], $user);

        return redirect()
            ->route('students.show', ['student' => $student])
            ->success('Student entry session updated successfully.');
    }
}
