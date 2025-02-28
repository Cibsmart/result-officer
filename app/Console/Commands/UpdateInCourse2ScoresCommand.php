<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\RawResult;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

final class UpdateInCourse2ScoresCommand extends Command
{
    protected $signature = 'rp:update-incourse2-scores';

    protected $description = 'Go through all raw results and update in_course_2';

    public function handle(): void
    {
        $count = RawResult::query()
            ->whereNotNull('result_id')
            ->where('status', 'processed')
            ->where('in_course_2', 0)
            ->whereRaw('in_course + exam <> total')
            ->count();

        $bar = $this->output->createProgressBar($count);

        $bar->start();

        $batchSize = 5_000;

        do {
            $updated = RawResult::query()
                ->whereNotNull('result_id')
                ->where('status', 'processed')
                ->where('in_course_2', 0)
                ->whereRaw('in_course + exam <> total')
                ->limit($batchSize)
                ->update(['in_course_2' => DB::raw('total - (in_course + exam)')]);

            $bar->advance($updated);
        } while ($updated > 0);

        $bar->finish();
    }
}
