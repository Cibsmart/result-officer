<?php

declare(strict_types=1);

namespace App\Data\Vetting;

use App\Data\Students\StudentBasicInfoData;
use App\Enums\StatusColor;
use App\Enums\VettingEventStatus;
use App\Models\Student;
use App\Models\VettingEvent;
use Spatie\LaravelData\Data;

final class VettingData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly VettingEventStatus $status,
        public readonly StatusColor $statusColor,
        public readonly StudentBasicInfoData $student,
    ) {
    }

    public static function fromModel(VettingEvent $vettingEvent): self
    {
        $student = $vettingEvent->student;
        assert($student instanceof Student);

        $status = $vettingEvent->status;

        return new self(
            id: $vettingEvent->id,
            status: $status,
            statusColor: $status->color(),
            student: StudentBasicInfoData::fromModel($student),
        );
    }
}
