<?php

declare(strict_types=1);

namespace App\Data\Vetting;

use App\Enums\StudentStatus;
use App\Enums\VettingEventStatus;
use App\Enums\VettingStatus;
use App\Models\Student;
use Spatie\LaravelData\Data;

final class VettingStudentData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $registrationNumber,
        public readonly StudentStatus $studentStatus,
        public readonly VettingEventStatus $vettingStatus,
        public readonly VettingStatus $vettingReport,
    ) {
    }

    public static function fromModel(Student $student): self
    {
        $vettingEvent = $student->vettingEvent;

        [$vettingStatus, $vettingReport] = $vettingEvent
            ? [$vettingEvent->status, $vettingEvent->vetting_status]
            : [VettingEventStatus::PENDING, VettingStatus::PENDING];

        return new self(
            id: $student->id,
            name: $student->name,
            registrationNumber: $student->registration_number,
            studentStatus: $student->status,
            vettingStatus: $vettingStatus,
            vettingReport: $vettingReport,
        );
    }
}
