<?php

declare(strict_types=1);

namespace App\Actions\Vetting;

use App\Enums\VettingStatus;
use App\Enums\VettingType;
use App\Models\Student;
use App\Models\VettingEvent;
use App\Models\VettingReport;
use App\Models\VettingStep;
use Illuminate\Database\Eloquent\Model;

abstract class ReportVettingStep
{
    private string $message = '';

    private VettingStep $vettingStep;

    abstract public function execute(Student $student): VettingStatus;

    public function report(): string
    {
        return $this->message;
    }

    public function vettingStep(): VettingStep
    {
        return $this->vettingStep;
    }

    public function updateReport(string $message): void
    {
        $this->message .= $message;
    }

    public function createReport(
        Model $model,
        string $message,
    ): void {
        VettingReport::updateOrCreateUsingModel($model, $this->vettingStep, VettingStatus::FAILED);

        $this->updateReport($message);
    }

    public function createVettingStep(Student $student, VettingType $vettingType): void
    {
        $vettingEvent = $student->vettingEvent;
        assert($vettingEvent instanceof VettingEvent);

        $this->vettingStep = VettingStep::getOrCreateUsingVettingEvent($vettingEvent, $vettingType);
    }
}
