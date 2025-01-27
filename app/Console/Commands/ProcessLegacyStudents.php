<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Data\Models\StudentModelData;
use App\Enums\RawDataStatus;
use App\Models\LegacyStudent;
use Exception;
use Illuminate\Console\Command;

final class ProcessLegacyStudents extends Command
{
    protected $signature = 'app:process_legacy_students';

    protected $description = 'Process legacy students';

    public function handle(): void
    {
        $students = LegacyStudent::query()->where('process_status', RawDataStatus::PENDING)->lazyById();

        $bar = $this->output->createProgressBar(count($students));

        $bar->start();

        foreach ($students as $student) {
            try {
                $model = StudentModelData::fromLegacyStudent($student)->getModel();

                $model->save();

                $student->student_id = $model->fresh()->id;
                $student->process_status = RawDataStatus::PROCESSED->value;
                $student->save();

            } catch (Exception $e) {
                $student->message = $e->getMessage();
                $student->process_status = RawDataStatus::FAILED->value;
                $student->save();
            }

            $bar->advance();
        }

        $bar->finish();
    }
}
