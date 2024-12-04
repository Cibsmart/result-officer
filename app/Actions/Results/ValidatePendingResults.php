<?php

declare(strict_types=1);

namespace App\Actions\Results;

use App\Models\ResultDetail;

final class ValidatePendingResults
{
    public function execute(): void
    {
        $pendingValidationResults = ResultDetail::query()
            ->with('result')
            ->where('validate', true)
            ->lazyById();

        foreach ($pendingValidationResults as $resultDetail) {
            $result = $resultDetail->result;

            if ($resultDetail->value !== $result->getData()) {
                $resultDetail->invalidate();

                continue;
            }

            $resultDetail->validate();
        }
    }
}
