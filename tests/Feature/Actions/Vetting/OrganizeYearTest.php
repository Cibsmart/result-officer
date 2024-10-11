<?php

declare(strict_types=1);

use App\Actions\Vetting\OrganizeYear;
use App\Enums\YearEnum;
use Tests\Factories\EnrollmentFactory;
use Tests\Factories\LevelFactory;
use Tests\Factories\SessionFactory;
use Tests\Factories\StudentFactory;

it('can correctly organize study year original set to all first', function (): void {
    $session = SessionFactory::new()->createOne(['name' => '2009/2010']);
    $session2 = SessionFactory::new()->createOne(['name' => '2010/2011']);
    $session3 = SessionFactory::new()->createOne(['name' => '2011/2012']);
    $session4 = SessionFactory::new()->createOne(['name' => '2012/2013']);

    $level = LevelFactory::new()->createOne();

    $student = StudentFactory::new()->has(
        EnrollmentFactory::new()
            ->count(4)
            ->sequence(
                ['session_id' => $session->id, 'level_id' => $level->id], ['session_id' => $session2->id],
                ['session_id' => $session3->id], ['session_id' => $session4->id],
            ),
    )->createOne(['entry_session_id' => $session->id, 'entry_level_id' => $level->id]);

    (new OrganizeYear())->execute($student);

    $enrollments = $student->fresh()->enrollments;

    expect($enrollments->count())->toBe(4)
        ->and($enrollments->firstWhere('session_id', $session->id)->year)->toBe(YearEnum::FIRST)
        ->and($enrollments->firstWhere('session_id', $session2->id)->year)->toBe(YearEnum::SECOND)
        ->and($enrollments->firstWhere('session_id', $session3->id)->year)->toBe(YearEnum::THIRD)
        ->and($enrollments->firstWhere('session_id', $session4->id)->year)->toBe(YearEnum::FOURTH);
});

it('can correctly organize study year original set in descending order', function (): void {
    $session = SessionFactory::new()->createOne(['name' => '2009/2010']);
    $session2 = SessionFactory::new()->createOne(['name' => '2010/2011']);
    $session3 = SessionFactory::new()->createOne(['name' => '2011/2012']);
    $session4 = SessionFactory::new()->createOne(['name' => '2012/2013']);

    $level = LevelFactory::new()->createOne();

    $student = StudentFactory::new()->has(
        EnrollmentFactory::new()
            ->count(4)
            ->sequence(
                ['session_id' => $session->id, 'level_id' => $level->id, 'year' => YearEnum::FOURTH],
                ['session_id' => $session2->id, 'year' => YearEnum::THIRD],
                ['session_id' => $session3->id, 'year' => YearEnum::SECOND],
                ['session_id' => $session4->id, 'year' => YearEnum::FIRST],
            ),
    )->createOne(['entry_session_id' => $session->id, 'entry_level_id' => $level->id]);

    (new OrganizeYear())->execute($student);

    $enrollments = $student->fresh()->enrollments;

    expect($enrollments->count())->toBe(4)
        ->and($enrollments->firstWhere('session_id', $session->id)->year)->toBe(YearEnum::FIRST)
        ->and($enrollments->firstWhere('session_id', $session2->id)->year)->toBe(YearEnum::SECOND)
        ->and($enrollments->firstWhere('session_id', $session3->id)->year)->toBe(YearEnum::THIRD)
        ->and($enrollments->firstWhere('session_id', $session4->id)->year)->toBe(YearEnum::FOURTH);
});
