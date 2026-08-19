<?php

declare(strict_types=1);

use App\Enums\Role;

it('labels every creatable role the same way getLabel() does', function (): void {
    foreach (Role::creatable() as $value => $label) {
        expect(Role::from($value)->getLabel())->toBe($label);
    }
});

it('offers every role except super admin for creation', function (): void {
    expect(array_keys(Role::creatable()))
        ->not->toContain(Role::SUPER_ADMIN->value)
        ->toHaveCount(count(Role::cases()) - 1);
});

it('does not swap the desk officer and exam officer labels', function (): void {
    expect(Role::creatable()[Role::DESK_OFFICER->value])->toBe('DESK OFFICER')
        ->and(Role::creatable()[Role::EXAM_OFFICER->value])->toBe('EXAM OFFICER');
});
