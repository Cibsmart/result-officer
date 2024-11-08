<?php

declare(strict_types=1);

namespace App\Actions\Vetting;

use App\Enums\VettingStatus;
use App\Models\VettingReport;
use App\Models\VettingStep;
use Illuminate\Database\Eloquent\Model;

abstract class ReportVettingStep
{
    private string $message = '';

    public function report(): string
    {
        return $this->message;
    }

    public function updateReport(string $message): void
    {
        $this->message .= $message;
    }

    public function createReport(
        Model $model,
        VettingStep $vettingStep,
        string $message,
    ): void {
        VettingReport::updateOrCreateUsingModel($model, $vettingStep, VettingStatus::FAILED);

        $this->updateReport($message);
    }
}
