<?php

declare(strict_types=1);

namespace App\Console\Commands\OneTime;

use App\Models\RawResult;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

final class UpdateInCourse2ScoresCommand extends Command
{
    protected $signature = 'rp:update-incourse2-scores';

    protected $description = 'Go through all raw results and update in_course_2';

    public function handle(): void
    {
        $query = RawResult::query()
            ->whereNotNull('result_id')
            ->where('status', 'processed')
            ->where('in_course_2', 0)
            ->where('total', '<>', '')
            ->whereRaw('in_course + exam <> total')
            ->orderBy('id');

        $bar = $this->output->createProgressBar($query->count());

        $bar->start();

        $batchSize = 5_000;

        do {
            $updated = $query->limit($batchSize)->update(['in_course_2' => DB::raw('total - (in_course + exam)')]);

            $bar->advance($updated);
        } while ($updated > 0);

        $bar->finish();
    }
}
