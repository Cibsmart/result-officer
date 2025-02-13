<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Results\ValidatePendingResults;
use Illuminate\Console\Command;

final class RunPendingResultsValidation extends Command
{
    protected $signature = 'rp:validate-results';

    protected $description = 'Validate results pending validation';

    public function __invoke(ValidatePendingResults $action): int
    {
        $action->execute();

        return Command::SUCCESS;
    }
}
