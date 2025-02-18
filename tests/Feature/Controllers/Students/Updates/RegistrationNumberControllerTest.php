<?php

declare(strict_types=1);

use App\Models\DBMail;
use App\Models\StudentHistory;
use Illuminate\Support\Str;
use Tests\Factories\StudentFactory;
use Tests\Factories\UserFactory;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

it('validates registration number update request', function (): void {
    $user = UserFactory::new()->createOne();
    $student = StudentFactory::new()->createOne();

    $response = actingAs($user)
        ->patch(route('student.registrationNumber.update', $student));

    $response->assertSessionHasErrors(['registration_number', 'remark', 'has_mail']);
});

it('validates unchanged registration number', function (): void {
    $user = UserFactory::new()->createOne();
    $student = StudentFactory::new()->createOne();
    $newRegistrationNumber = $student->registration_number . 'A';

    $response = actingAs($user)
        ->patch(route('student.registrationNumber.update', $student), [
            'has_mail' => true,
            'registration_number' => $newRegistrationNumber,
            'remark' => 'Updated registration number according to request',
        ]);

    $response->assertSessionHasErrors(['mail_title', 'mail_date']);
});

it('can update students registration number without mail information', function (): void {
    $user = UserFactory::new()->createOne();
    $student = StudentFactory::new()->createOne();
    $oldRegistrationNumber = $student->registration_number;
    $newRegistrationNumber = $student->registration_number . 'A';

    $response = actingAs($user)
        ->patch(route('student.registrationNumber.update', $student), [
            'has_mail' => false,
            'registration_number' => $newRegistrationNumber,
            'remark' => 'Updated registration number according to request',
        ]);

    $response->assertRedirect(route('students.show', $student->fresh()));

    assert($student->fresh()->registration_number === $newRegistrationNumber);
    assertDatabaseHas(StudentHistory::class, [
        'data' => json_encode(['new' => $newRegistrationNumber, 'old' => $oldRegistrationNumber]),
        'student_id' => $student->id,
    ]);
});

it('must provide mail details when the update has mail', function (): void {
    $user = UserFactory::new()->createOne();
    $student = StudentFactory::new()->createOne();

    $response = actingAs($user)
        ->patch(route('student.registrationNumber.update', $student), [
            'registration_number' => $student->registration_number,
        ]);

    $response->assertSessionHasErrors(['registration_number']);
});

it('can update students registration number with mail information', function (): void {
    $user = UserFactory::new()->createOne();
    $student = StudentFactory::new()->createOne();
    $oldRegistrationNumber = $student->registration_number;
    $newRegistrationNumber = $student->registration_number . 'A';

    $mailTitle = 'Registration number update';
    $response = actingAs($user)
        ->patch(route('student.registrationNumber.update', $student), [
            'has_mail' => true,
            'mail_date' => now()->subDay()->toDateString(),
            'mail_title' => $mailTitle,
            'registration_number' => $newRegistrationNumber,
            'remark' => 'Updated registration number according to request',
        ]);

    $response->assertRedirect(route('students.show', $student->fresh()));

    assert($student->fresh()->registration_number === $newRegistrationNumber);

    assertDatabaseHas(StudentHistory::class, [
        'data' => json_encode(['new' => $newRegistrationNumber, 'old' => $oldRegistrationNumber]),
        'student_id' => $student->id,
    ]);

    assertDatabaseHas(DBMail::class, [
        'title' => Str::upper($mailTitle),
        'user_id' => $user->id,
    ]);
});
