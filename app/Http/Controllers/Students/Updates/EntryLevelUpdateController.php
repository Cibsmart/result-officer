<?php

declare(strict_types=1);

namespace App\Http\Controllers\Students\Updates;

use App\Actions\Students\Updates\EntryLevelUpdateAction;
use App\Models\Level;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class EntryLevelUpdateController
{
    public function __invoke(
        Student $student,
        Request $request,
        EntryLevelUpdateAction $action,
    ): RedirectResponse {
        $validated = $request->validate(['entry_level_id' => ['required']]);

        $user = $request->user();
        assert($user instanceof User);

        $newValue = Level::getUsingId($validated['entry_level_id']);

        $action->execute($student, $newValue, $validated['remark'], $user);

        return redirect()
            ->route('students.show', ['student' => $student])
            ->success('Student entry level updated successfully.');
    }
}
