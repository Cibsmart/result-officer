<?php

declare(strict_types=1);

namespace App\Http\Controllers\Students\Updates;

use App\Actions\Students\Updates\BirthDateUpdateAction;
use App\Models\Student;
use App\Models\User;
use App\Values\DateValue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

final class BirthDateUpdateController
{
    public function __invoke(
        Student $student,
        Request $request,
        BirthDateUpdateAction $action,
    ): RedirectResponse {
        $validated = $request->validate([
            'date_of_birth' => [
                'required', 'regex:/^\d{4}-\d{2}-\d{2}$/',
                Rule::date()->before(now()->subYears(15)),
            ],
            'remark' => ['required', 'string', 'min:3', 'max:255'],
        ]);

        $user = $request->user();
        assert($user instanceof User);

        $newValue = DateValue::fromValue($validated['date_of_birth']);

        $action->execute($student, $newValue, $validated['remark'], $user);

        return redirect()
            ->route('students.show', ['student' => $student])
            ->success('Student date of birth updated successfully.');
    }
}
