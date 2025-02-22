<?php

declare(strict_types=1);

namespace App\Http\Controllers\Students\Updates;

use App\Actions\Students\Updates\GenderUpdateAction;
use App\Enums\Gender;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class GenderUpdateController
{
    public function __invoke(
        Student $student,
        Request $request,
        GenderUpdateAction $action,
    ): RedirectResponse {
        $validated = $request->validate(['gender' => ['required']]);

        $user = $request->user();
        assert($user instanceof User);

        $newValue = Gender::from($validated['gender']);

        $action->execute($student, $newValue, $validated['remark'], $user);

        return redirect()
            ->route('students.show', ['student' => $student])
            ->success('Student gender updated successfully.');
    }
}
