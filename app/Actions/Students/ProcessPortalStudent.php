<?php

declare(strict_types=1);

namespace App\Actions\Students;

use App\Enums\RawDataStatus;
use App\Models\RawStudent;
use App\Models\Student;

final class ProcessPortalStudent
{
    public function execute(RawStudent $rawStudent): void
    {
        $exists = Student::query()
            ->where('registration_number', $rawStudent->registration_number)
            ->exists();

        if ($exists) {
            $rawStudent->updateStatus(RawDataStatus::DUPLICATE);

            return;
        }

        //TODO: Create new student

        $rawStudent->updateStatus(RawDataStatus::PROCESSED);
    }
}
