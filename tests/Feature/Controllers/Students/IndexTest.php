<?php

declare(strict_types=1);

use App\Data\Students\StudentBasicData;
use App\Models\Student;
use Tests\Factories\StudentFactory;
use Tests\Factories\UserFactory;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('redirects guest to login', function (): void {

    get(route('students.index'))->assertRedirect('login');
});

it('loads the correct component', function (): void {
    $user = UserFactory::new()->createOne();

    actingAs($user)->get(route('students.index'))
        ->assertHasComponent('students/index/page');
});

it('passes paginated student data to the view', function (): void {
    $user = UserFactory::new()->createOne();
    StudentFactory::new()->count(3)->create();

    $students = Student::query()
        ->with('program.department.faculty', 'entrySession', 'lga.state.country')
        ->paginate();

    actingAs($user)
        ->get(route('students.index'))
        ->assertHasPaginatedData('paginated', StudentBasicData::collect($students)->withPath(route('students.index')));
});
