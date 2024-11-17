<?php

declare(strict_types=1);

namespace App\Services\Vetting\Steps;

use App\Actions\Vetting\VerifyFailedCourses;
use App\Contracts\VettingService;
use App\Models\VettingEvent;

final readonly class CheckFailedCoursesStep implements VettingService
{
    public function __construct(private VerifyFailedCourses $action)
    {
    }

    public function check(VettingEvent $vettingEvent): void
    {
        $student = $vettingEvent->student;

        $status = $this->action->execute($student);

        $this->action->vettingStep()->updateStatusAndRemarks($status, $this->action->report());
    }
}
