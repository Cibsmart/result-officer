<?php

declare(strict_types=1);

use App\Actions\Vetting\OrganizeYear;
use App\Enums\Year;
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

    $organize = new OrganizeYear();

    $organize->execute($student);

    expect($organize->report())->toBe('RE-ORGANIZED STUDY YEARS');

    $enrollments = $student->fresh()->enrollments;

    expect($enrollments->count())->toBe(4)
        ->and($enrollments->firstWhere('session_id', $session->id)->year)->toBe(Year::FIRST)
        ->and($enrollments->firstWhere('session_id', $session2->id)->year)->toBe(Year::SECOND)
        ->and($enrollments->firstWhere('session_id', $session3->id)->year)->toBe(Year::THIRD)
        ->and($enrollments->firstWhere('session_id', $session4->id)->year)->toBe(Year::FOURTH);
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
                ['session_id' => $session->id, 'level_id' => $level->id, 'year' => Year::FOURTH],
                ['session_id' => $session2->id, 'year' => Year::THIRD],
                ['session_id' => $session3->id, 'year' => Year::SECOND],
                ['session_id' => $session4->id, 'year' => Year::FIRST],
            ),
    )->createOne(['entry_session_id' => $session->id, 'entry_level_id' => $level->id]);

    $organize = new OrganizeYear();

    $organize->execute($student);

    expect($organize->report())->toBe('RE-ORGANIZED STUDY YEARS');

    $enrollments = $student->fresh()->enrollments;

    expect($enrollments->count())->toBe(4)
        ->and($enrollments->firstWhere('session_id', $session->id)->year)->toBe(Year::FIRST)
        ->and($enrollments->firstWhere('session_id', $session2->id)->year)->toBe(Year::SECOND)
        ->and($enrollments->firstWhere('session_id', $session3->id)->year)->toBe(Year::THIRD)
        ->and($enrollments->firstWhere('session_id', $session4->id)->year)->toBe(Year::FOURTH);
});

it('does not re-organize study year for already organized year', function (): void {
    $session = SessionFactory::new()->createOne(['name' => '2009/2010']);
    $session2 = SessionFactory::new()->createOne(['name' => '2010/2011']);
    $session3 = SessionFactory::new()->createOne(['name' => '2011/2012']);
    $session4 = SessionFactory::new()->createOne(['name' => '2012/2013']);

    $level = LevelFactory::new()->createOne();

    $student = StudentFactory::new()->has(
        EnrollmentFactory::new()
            ->count(4)
            ->sequence(
                ['session_id' => $session->id, 'level_id' => $level->id, 'year' => Year::FIRST],
                ['session_id' => $session2->id, 'year' => Year::SECOND],
                ['session_id' => $session3->id, 'year' => Year::THIRD],
                ['session_id' => $session4->id, 'year' => Year::FOURTH],
            ),
    )->createOne(['entry_session_id' => $session->id, 'entry_level_id' => $level->id]);

    $organize = new OrganizeYear();
    $organize->execute($student);

    expect($organize->report())->toBe('PASSED');
});
