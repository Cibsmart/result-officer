<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\LegacyFinalResult;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

final class LegacyFinalResultSeeder extends Seeder
{
    public function run(): void
    {
        $path = Storage::path('seeders/legacy_final_results.csv');

        $handle = fopen($path, 'r');

        if (! $handle) {
            return;
        }

        $counter = 0;

        while (($data = fgetcsv($handle))) {
            $counter += 1;

            if ($counter === 1) {
                continue;
            }

            $this->insertResult($data);
        }
    }

    /** @param array<int, string> $result */
    private function insertResult(array $result): void
    {
        LegacyFinalResult::query()->create([
            'cleared_month' => $result[16],
            'cleared_upload_date' => $result[17],
            'cleared_year' => $result[15],
            'course_code' => $result[11],
            'course_title' => $result[12],
            'credit_unit' => $result[6],
            'db_officers' => $result[18],
            'exam' => $result[5],
            'examiner' => $result[13],
            'exam_date' => $result[14],
            'exam_officer' => $result[19],
            'inc' => $result[4],
            'legacy_course_id' => $result[9],
            'legacy_id' => $result[0],
            'level' => $result[10],
            'name' => $result[2],
            'old_registration_number' => $result[20],
            'registration_number' => $result[3],
            'semester' => $result[7],
            'session' => $result[8],
            'student_id' => $result[1],
        ]);
    }
}
