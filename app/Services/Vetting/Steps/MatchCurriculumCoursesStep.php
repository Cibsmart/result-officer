<?php

declare(strict_types=1);

namespace App\Services\Vetting\Steps;

use App\Actions\Vetting\MatchCurriculumCourses;
use App\Contracts\VettingService;
use App\Enums\VettingType;
use App\Models\VettingEvent;
use App\Models\VettingStep;

final readonly class MatchCurriculumCoursesStep implements VettingService
{
    public function __construct(private MatchCurriculumCourses $action)
    {
    }

    public function check(VettingEvent $vettingEvent): void
    {
        $student = $vettingEvent->student;

        $vettingStep = VettingStep::getOrCreateUsingVettingEvent($vettingEvent, VettingType::MATCH_COURSES);

        $status = $this->action->execute($student, $vettingStep);

        $vettingStep->updateStatusAndRemarks($status, $this->action->report());
    }
}
