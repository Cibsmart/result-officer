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
            'cleared_month' => $result[15],
            'cleared_upload_date' => $result[16],
            'cleared_year' => $result[14],
            'course_code' => $result[10],
            'course_title' => $result[11],
            'credit_unit' => $result[6],
            'db_officers' => $result[17],
            'exam' => $result[5],
            'examiner' => $result[12],
            'exam_date' => $result[13],
            'exam_officer' => $result[18],
            'inc' => $result[4],
            'legacy_course_id' => $result[9],
            'legacy_id' => $result[0],
            'name' => $result[2],
            'old_registration_number' => $result[19],
            'registration_number' => $result[3],
            'semester' => $result[7],
            'session' => $result[8],
            'student_id' => $result[1],
        ]);
    }
}
