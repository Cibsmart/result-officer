<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Results\ValidatePendingResults;
use Illuminate\Console\Command;

final class RunPendingResultsValidation extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'rp:results-validate';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Validate results pending validation';

    /**
     * Execute the console command.
     */
    public function handle(ValidatePendingResults $action): int
    {
        $action->execute();

        return Command::SUCCESS;
    }
}
