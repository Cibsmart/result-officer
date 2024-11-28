<?php

declare(strict_types=1);

namespace App\Actions\Results;

use App\Models\ResultDetail;

final class ValidatePendingResults
{
    public function execute(): void
    {
        $pendingValidationResults = ResultDetail::query()
            ->where('validate', true)
            ->lazyById();

        foreach ($pendingValidationResults as $result) {
            $result->data = $result->value;
            $result->validate = false;
            $result->save();
        }
    }
}
