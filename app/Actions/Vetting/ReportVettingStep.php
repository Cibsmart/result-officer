<?php

declare(strict_types=1);

namespace App\Actions\Vetting;

abstract class ReportVettingStep
{
    private string $report = '';

    public function report(): string
    {
        return $this->report;
    }

    public function updateReport(string $report): void
    {
        $this->report .= $report;
    }
}
