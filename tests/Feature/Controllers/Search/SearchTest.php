<?php

declare(strict_types=1);

use App\Enums\Role;
use App\Models\User;
use Tests\Factories\CourseFactory;
use Tests\Factories\StudentFactory;
use Tests\Factories\UserFactory;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

/**
 * Defaults to a desk officer: the read-only role, institution-wide but not an
 * administrator. `user` no longer works here — it holds no permissions at all,
 * so it cannot reach search.
 */
function searchUser(Role $role = Role::DESK_OFFICER): User
{
    return UserFactory::new()->role($role)->createOne();
}

it('requires authentication', function (): void {
    get(route('search', ['q' => 'anything']))->assertRedirect(route('login'));
    get(route('search.suggestions', ['q' => 'anything']))->assertRedirect(route('login'));
});

it('finds a student by registration number', function (): void {
    $student = StudentFactory::new()->createOne(['registration_number' => 'EBSU/2019/12345']);

    $response = actingAs(searchUser())->getJson(route('search.suggestions', ['q' => '2019/12345']));

    $response->assertOk();

    $groups = $response->json('groups');

    expect($groups)->toHaveCount(1)
        ->and($groups[0]['key'])->toBe('students')
        ->and($groups[0]['results'])->toHaveCount(1)
        ->and($groups[0]['results'][0]['id'])->toBe($student->id)
        ->and($groups[0]['results'][0]['url'])->toBe(route('students.show', ['student' => $student], absolute: false));
});

it('finds a student by any part of their name', function (): void {
    StudentFactory::new()->createOne(['first_name' => 'Chidinma', 'last_name' => 'Okonkwo']);

    $response = actingAs(searchUser())->getJson(route('search.suggestions', ['q' => 'okonk']));

    expect($response->json('groups.0.results'))->toHaveCount(1);
});

it('hides admin-only groups from a non-admin', function (): void {
    CourseFactory::new()->createOne(['code' => 'MTH101', 'title' => 'Algebra']);

    $response = actingAs(searchUser())->getJson(route('search.suggestions', ['q' => 'MTH101']));

    expect($response->json('groups'))->toBe([]);
});

it('shows the courses group to an admin', function (): void {
    CourseFactory::new()->createOne(['code' => 'MTH101', 'title' => 'Algebra']);

    $response = actingAs(searchUser(Role::ADMIN))->getJson(route('search.suggestions', ['q' => 'MTH101']));

    $groups = $response->json('groups');

    expect($groups)->toHaveCount(1)
        ->and($groups[0]['key'])->toBe('courses')
        ->and($groups[0]['results'][0]['title'])->toBe('MTH101');
});

it('returns nothing for a query below the minimum length', function (): void {
    StudentFactory::new()->createOne(['last_name' => 'Okonkwo']);

    $response = actingAs(searchUser())->getJson(route('search.suggestions', ['q' => 'o']));

    expect($response->json('groups'))->toBe([]);
});

it('renders the full search page with its results', function (): void {
    StudentFactory::new()->createOne(['last_name' => 'Okonkwo']);

    actingAs(searchUser())
        ->get(route('search', ['q' => 'okonkwo']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('search/page')
            ->where('query', 'okonkwo')
            ->has('groups.0.results', 1));
});
