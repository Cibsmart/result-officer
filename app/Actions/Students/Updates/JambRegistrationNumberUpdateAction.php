<?php

declare(strict_types=1);

namespace App\Actions\Students\Updates;

use App\Enums\StudentField;
use App\Models\Student;

final class JambRegistrationNumberUpdateAction
{
    public function execute(Student $student, string $newValue): void
    {
        $student->updateField(StudentField::JAMB_REGISTRATION_NUMBER, $newValue);
    }
}
