<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\SemesterEnrollment;
use App\Models\Student;
use Illuminate\Console\Command;
use Illuminate\Console\Prohibitable;
use Illuminate\Support\Facades\Log;

final class DeleteStudent extends Command
{
    use Prohibitable;

    protected $signature = 'rp:delete-student {registrationNumber}';

    protected $description = 'Force Deletes a Student Record with all its Enrollments, Registrations, and Results';

    public function handle(): int
    {
        $student = Student::getUsingRegistrationNumber($this->argument('registrationNumber'));

        $sessionEnrollments = $student->sessionEnrollments;

        Log::info('Deleting Student ...: ' . $student->registration_number);

        foreach ($sessionEnrollments as $sessionEnrollment) {
            foreach ($sessionEnrollment->semesterEnrollments as $semesterEnrollment) {
                $this->deleteRegistrations($semesterEnrollment);

                $semesterEnrollment->delete();
            }

            $sessionEnrollment->delete();
        }

        $student->forceDelete();
        Log::info('Deleted Student: ' . $student->registration_number);

        return Command::SUCCESS;
    }

    private function deleteRegistrations(SemesterEnrollment $semesterEnrollment): void
    {
        foreach ($semesterEnrollment->registrations as $registration) {
            $result = $registration->result;

            if ($result !== null) {
                $resultDetail = $result->resultDetail;

                $resultDetail->delete();

                $result->forceDelete();
            }

            $registration->forceDelete();
        }
    }
}
