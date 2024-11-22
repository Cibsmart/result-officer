<?php

declare(strict_types=1);

namespace App\Data\Vetting;

use App\Data\VettingReportData;
use App\Enums\StatusColor;
use App\Enums\StudentStatus;
use App\Enums\VettingEventStatus;
use App\Models\Student;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

final class VettingStudentData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $registrationNumber,
        public readonly StudentStatus $studentStatus,
        public readonly VettingEventStatus $vettingStatus,
        public readonly StatusColor $vettingStatusColor,
        /** @var \Illuminate\Support\Collection<int, \App\Data\VettingReportData> @vettingSteps */
        public readonly Collection $vettingSteps,
    ) {
    }

    public static function fromModel(Student $student): self
    {
        $vettingEvent = $student->vettingEvent;

        [$vettingStatus, $vettingSteps] = $vettingEvent
            ? [$vettingEvent->status, $vettingEvent->vettingSteps]
            : [VettingEventStatus::PENDING, collect([])];

        $status = $student->status;
        assert($status instanceof StudentStatus);

        return new self(
            id: $student->id,
            name: $student->name,
            registrationNumber: $student->registration_number,
            studentStatus: $status,
            vettingStatus: $vettingStatus,
            vettingStatusColor: $vettingStatus->color(),
            vettingSteps: VettingReportData::collect($vettingSteps),
        );
    }
}
