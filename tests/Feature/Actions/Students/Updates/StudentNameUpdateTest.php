<?php

declare(strict_types=1);

use App\Actions\Students\Updates\StudentNameUpdate;
use App\Enums\ModifiableFields\StudentModifiableField;
use App\Models\StudentHistory;
use Tests\Factories\DBMailFactory;
use Tests\Factories\StudentFactory;
use Tests\Factories\UserFactory;

use function Pest\Laravel\assertDatabaseHas;

it('can update only the last name field', function (): void {
    $student = StudentFactory::new()->createOne();
    $newName = 'John ';

    $action = new StudentNameUpdate();

    $action->execute($student, ['last_name' => $newName]);

    $student = $student->fresh();

    expect($student->last_name)->toBe(Str::of($newName)->trim()->upper()->value());
});

it('can update only the first name field', function (): void {
    $student = StudentFactory::new()->createOne();
    $newName = 'John ';

    $action = new StudentNameUpdate();

    $action->execute($student, ['first_name' => $newName]);

    $student = $student->fresh();

    expect($student->first_name)->toBe(Str::of($newName)->trim()->upper()->value());
});

it('can update only the other name field', function (): void {
    $student = StudentFactory::new()->createOne();
    $newName = 'John';

    $action = new StudentNameUpdate();

    $action->execute($student, ['other_names' => $newName]);

    $student = $student->fresh();

    expect($student->other_names)->toBe(Str::of($newName)->trim()->upper()->value());
});

it('can update all the name fields', function (): void {
    $student = StudentFactory::new()->createOne();
    [$lastName, $firstName, $otherNames] = ['JOHN', 'DOE', 'OKEKE'];
    $oldStudentName = $student->name;

    $action = new StudentNameUpdate();

    $newName = ['other_names' => $otherNames, 'first_name' => $firstName, 'last_name' => $lastName];
    $action->execute($student, $newName);

    $student = $student->fresh();

    expect($student->other_names)->toBe($otherNames)
        ->and($student->first_name)->toBe($firstName)
        ->and($student->last_name)->toBe($lastName);

    $newStudentName = $student->name;

    assertDatabaseHas(StudentHistory::class, [
        'data' => json_encode(['new' => $newStudentName, 'old' => $oldStudentName]),
        'field' => StudentModifiableField::NAME,
        'student_id' => $student->id,
    ]);
});

it('set remark, user and dbmail field in student history', function (): void {
    $student = StudentFactory::new()->createOne();
    $user = UserFactory::new()->createOne();
    $dbMail = DBMailFactory::new()->createOne();
    $remark = 'Remark';

    $newName = 'JOHN';

    $action = new StudentNameUpdate();

    $action->execute($student, ['last_name' => $newName], remark: $remark, dbMail: $dbMail, user: $user);

    $student = $student->fresh();

    expect($student->last_name)->toBe($newName);

    assertDatabaseHas(StudentHistory::class, [
        'db_mail_id' => $dbMail->id,
        'remark' => $remark,
        'student_id' => $student->id,
        'user_id' => $user->id,
    ]);
});
