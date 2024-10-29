<?php

declare(strict_types=1);

use Inertia\Testing\AssertableInertia as Assert;
use Tests\Factories\UserFactory;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\withoutExceptionHandling;

test('student result view page loads', function (): void {
    withoutExceptionHandling();
    $numberOfStudents = 5;
    $user = UserFactory::new()->createOne();
    $student = createMultipleStudentsWithResults(numberOfStudents: $numberOfStudents)[0];

    $department = ['id' => $student->program->department->id];
    $session = ['id' => $student->entry_session_id];
    $level = ['id' => $student->entry_level_id];

    actingAs($user)
        ->from(route('summary.form'))
        ->post(route('summary.view',
            compact('department', 'session', 'level'),
        ))
        ->assertStatus(200)
        ->assertInertia(fn (Assert $page) => $page
            ->component('summary/view/page')
            ->has('department', fn (Assert $page) => $page
                ->has('department')
                ->has('session')
                ->has('level')
                ->has('students', $numberOfStudents),
            ),
        );
});
