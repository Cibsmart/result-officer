<?php

declare(strict_types=1);

use App\Models\Student;

use function Pest\Laravel\assertDatabaseMissing;

it('can delete student and all its associated models', function (): void {
    $student = createStudentWithResults();

    $this->artisan('rp:delete-student', ['registrationNumber' => $student->registration_number])
        ->assertExitCode(0);

    assertDatabaseMissing(Student::class, [
        'id' => $student->id,
    ]);
});
