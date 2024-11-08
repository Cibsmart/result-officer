<?php

declare(strict_types=1);

namespace App\Services\Vetting\Steps;

use App\Actions\Vetting\OrganizeStudyYear;
use App\Contracts\VettingService;
use App\Models\VettingEvent;

use function PHPUnit\Framework\assertNotNull;

final readonly class OrganizeStudyYearStep implements VettingService
{
    public function __construct(private OrganizeStudyYear $action)
    {
    }

    public function check(VettingEvent $vettingEvent): void
    {
        $student = $vettingEvent->student;

        assertNotNull($student);

        $this->action->execute($student);
    }
}
