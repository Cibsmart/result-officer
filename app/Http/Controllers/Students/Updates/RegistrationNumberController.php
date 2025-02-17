<?php

declare(strict_types=1);

namespace App\Http\Controllers\Students\Updates;

use App\Actions\Students\Updates\RegistrationNumberUpdate;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;

final class RegistrationNumberController
{
    public function __invoke(
        Student $student,
        RegistrationNumberRequest $request,
        RegistrationNumberUpdate $action,
    ): RedirectResponse {

        $action->execute($student, $request->string('registration_number')->value());

        return redirect()
            ->route('students.show', ['student' => $student])
            ->success('Registration number updated.');
    }
}
