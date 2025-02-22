<?php

declare(strict_types=1);

namespace App\Actions\Students\Updates;

use App\Enums\StudentField;
use App\Models\Student;

final class StudentEmailUpdateAction
{
    public function execute(Student $student, string $newValue): void
    {
        $student->updateField(StudentField::EMAIL, $newValue);
    }
}
