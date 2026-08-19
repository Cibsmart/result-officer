<?php

declare(strict_types=1);

namespace App\Console\Commands\OneTime;

use App\Enums\ClassOfDegree;
use App\Models\FinalStudent;
use Illuminate\Console\Command;

/**
 * Remediation aid for the ClassOfDegree range gaps.
 *
 * The old bands were closed ranges with a 0.01-wide gap above each one, and a
 * value inside a gap fell through to FAIL. final_students stores the FCGPA to
 * three decimals, so cleared graduates could land in a gap and be recorded as
 * having failed. This reports every stored FCGPA whose classification changes
 * under the corrected bands.
 */
final class ReviewDegreeClassifications extends Command
{
    protected $signature = 'rp:review-degree-classifications';

    protected $description = 'Report graduands whose class of degree changes under the corrected ClassOfDegree bands';

    public function __invoke(): int
    {
        $affected = self::affectedRows();

        if ($affected === []) {
            $this->info('No stored FCGPA falls in a former range gap. Nothing to correct.');

            return Command::SUCCESS;
        }

        $this->warn(count($affected) . ' graduand record(s) were classified from a range gap.');
        $this->line('Each was recorded as the "Was" class and should be the "Now" class. Reissue affected results.');
        $this->newLine();

        $this->table(['Final student', 'Registration number', 'FCGPA', 'Was', 'Now'], $affected);

        return Command::SUCCESS;
    }

    /** @return list<array{0: int, 1: string, 2: string, 3: string, 4: string}> */
    private static function affectedRows(): array
    {
        $affected = [];

        $finalStudents = FinalStudent::query()
            ->with('student:id,registration_number')
            ->orderBy('id')
            ->cursor();

        foreach ($finalStudents as $finalStudent) {
            $fcgpa = $finalStudent->final_cumulative_grade_point_average;
            $was = self::classifyWithGaps($fcgpa);
            $now = ClassOfDegree::for($fcgpa);

            if ($was === $now) {
                continue;
            }

            $student = $finalStudent->student;

            $affected[] = [
                $finalStudent->id,
                $student === null ? '—' : $student->registration_number,
                number_format($fcgpa, 3),
                $was->value,
                $now->value,
            ];
        }

        return $affected;
    }

    /** The pre-fix banding, kept here only so affected rows can be identified. */
    private static function classifyWithGaps(float $fcgpa): ClassOfDegree
    {
        return match (true) {
            $fcgpa >= 4.50 && $fcgpa <= 5.00 => ClassOfDegree::FIRST_CLASS,
            $fcgpa >= 3.50 && $fcgpa <= 4.49 => ClassOfDegree::SECOND_CLASS_UPPER,
            $fcgpa >= 2.50 && $fcgpa <= 3.49 => ClassOfDegree::SECOND_CLASS_LOWER,
            $fcgpa >= 1.50 && $fcgpa <= 2.49 => ClassOfDegree::THIRD_CLASS,
            $fcgpa >= 1.00 && $fcgpa <= 1.49 => ClassOfDegree::PASS,
            default => ClassOfDegree::FAIL,
        };
    }
}
