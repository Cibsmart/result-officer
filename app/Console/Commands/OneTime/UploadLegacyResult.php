<?php

declare(strict_types=1);

namespace App\Console\Commands\OneTime;

use App\Models\LegacyResult;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

final class UploadLegacyResult extends Command
{
    protected $signature = 'app:upload-legacy-result';

    protected $description = 'Read CSV and Upload Legacy Results';

    public function handle(): void
    {
        $path = Storage::path('seeders/legacy_results.csv');

        $handle = fopen($path, 'r');

        if (! $handle) {
            return;
        }

        $bar = $this->output->createProgressBar(6_299_941);

        $counter = 0;

        $bar->start();

        while (($data = fgetcsv($handle))) {
            $counter += 1;

            if ($counter === 1) {
                continue;
            }

            $this->insertResult($data);

            $bar->advance();
        }

        $bar->finish();
    }

    /** @param non-empty-list<string|null> $result */
    private function insertResult(array $result): void
    {
        LegacyResult::query()->create([
            'course_code' => $result[12],
            'course_title' => $result[13],
            'credit_unit' => $result[7],
            'department' => $result[14],
            'exam' => $result[5],
            'examiner' => $result[15],
            'exam_date' => $result[16],
            'inc' => $result[4],
            'legacy_course_id' => $result[10],
            'legacy_id' => $result[0],
            'level' => $result[11],
            'name' => $result[2],
            'registration_number' => $result[3],
            'remark' => $result[6],
            'semester' => $result[8],
            'session' => $result[9],
            'student_id' => $result[1],
        ]);
    }
}
