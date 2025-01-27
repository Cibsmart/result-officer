<?php

declare(strict_types=1);

namespace App\Actions\Import\Students;

use App\Enums\RawDataStatus;
use App\Models\RawStudent;
use App\Models\Student;

final class ProcessPortalStudent
{
    /** @throws \Exception */
    public function execute(RawStudent $rawStudent): void
    {
        $exists = Student::query()
            ->where('registration_number', $rawStudent->getRegistrationNumber())
            ->exists();

        if ($exists) {
            $rawStudent->updateStatus(RawDataStatus::DUPLICATE);

            return;
        }

        $student = Student::createFromRawStudent($rawStudent);

        $rawStudent->updateStatusAndStudent(RawDataStatus::PROCESSED, $student);
    }
}
