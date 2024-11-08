<?php

declare(strict_types=1);

namespace App\Actions\Vetting;

use App\Enums\VettingStatus;
use App\Enums\VettingType;
use App\Models\SemesterEnrollment;
use App\Models\Session;
use App\Models\Student;
use App\Models\VettingStep;
use Illuminate\Support\Facades\Hash;

use function PHPUnit\Framework\assertNotNull;

final class ValidateResults
{
    private VettingStatus $report = VettingStatus::PASSED;

    private string $message = '';

    public function execute(Student $student): void
    {
        $sessionEnrollments = $student->sessionEnrollments()->with(['session', 'semesterEnrollments.semester'])->get();

        $vettingStep = $student->vettingEvent->vettingSteps()
            ->where('type', VettingType::VALIDATE_RESULTS)
            ->firstOrFail();

        foreach ($sessionEnrollments as $sessionEnrollment) {
            $semesterEnrollments = $sessionEnrollment->semesterEnrollments;

            foreach ($semesterEnrollments as $semesterEnrollment) {
                $session = $sessionEnrollment->session;

                assertNotNull($session);

                $this->validate($semesterEnrollment, $session, $vettingStep);
            }
        }
    }

    public function report(): VettingStatus
    {
        return $this->report;
    }

    private function validate(
        SemesterEnrollment $semesterEnrollment,
        Session $session,
        VettingStep $vettingStep,
    ): void {
        $registrations = $semesterEnrollment->registrations()->with('result', 'course')->get();

        foreach ($registrations as $registration) {
            $result = $registration->result;

            if (Hash::check($result->getData(), $result->data)) {
                continue;
            }

            assertNotNull($result);
            $modelName = $result::class;

            $result->vettable()->updateOrCreate(
                [
                    'vettable_id' => $result->id,
                    'vettable_type' => (new $modelName())->getMorphClass(),
                    'vetting_step_id' => $vettingStep->id,
                ],
                ['status' => VettingStatus::FAILED],
            );

            $code = $registration->course->code;
            $semester = $semesterEnrollment->semester;

            if ($this->report !== VettingStatus::FAILED) {
                $this->report = VettingStatus::FAILED;
            }

            $this->message .= "{$code} in {$semester->name} {$session->name} is invalid. \n";
        }
    }
}
