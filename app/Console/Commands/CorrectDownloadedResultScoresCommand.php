<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\RawDataStatus;
use App\Models\RawResult;
use App\Values\RegistrationNumber;
use App\Values\TotalScore;
use Exception;
use Illuminate\Console\Command;

final class CorrectDownloadedResultScoresCommand extends Command
{
    protected $signature = 'rp:correct-result-scores';

    protected $description = 'Select all portal download results and correct their scores';

    public function handle(): int
    {
        $bar = $this->output->createProgressBar(314_805);

        $bar->start();

        RawResult::query()
            ->with('result.registration.semesterEnrollment.sessionEnrollment.session', 'result.rawResult')
            ->select('raw_results.id', 'raw_results.in_course_2', 'raw_results.total', 'raw_results.status',
                'raw_results.result_id', 'raw_results.registration_number')
            ->where('status', RawDataStatus::UPDATING)
            ->orderBy('id')
            ->lazyById(500, column: 'id')
            ->each(function (RawResult $rawResult) use ($bar): void {
                try {
                    $this->processUpdate($rawResult);
                } catch (Exception $e) {
                    $rawResult->updateStatus(RawDataStatus::FAILED);
                    $rawResult->setMessage($e->getMessage());

                    return;
                }

                $bar->advance();
            });

        $bar->finish();

        return Command::SUCCESS;
    }

    private function processUpdate(RawResult $rawResult): void
    {
        $result = $rawResult->result;

        $registration = $result->registration;
        $resultDetail = $result->resultDetail;

        $registrationNumber = RegistrationNumber::new($rawResult->getRegistrationNumber());

        $scores = $result->getScores();

        $scores['in_course_2'] = $rawResult->in_course_2;

        $total = TotalScore::new((int) $rawResult->total);
        $grade = $total->grade($registrationNumber->allowEGrade() || $registration->session()->allowsEGrade());
        $gradePoint = $grade->point() * $registration->credit_unit->value;

        $result->update([
            'grade' => $grade->value, 'grade_point' => $gradePoint, 'scores' => $scores, 'total_score' => $total->value,
        ]);

        $resultDetail->update(['value' => "{$registration->id}-{$total->value}-{$grade->value}-{$gradePoint}"]);

        $rawResult->updateStatus(RawDataStatus::PROCESSED);

        unset($result, $registration, $registrationNumber, $scores, $total, $grade, $gradePoint, $rawResult);
    }
}
