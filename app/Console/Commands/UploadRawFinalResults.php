<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Import\Excel\ValidateHeadings;
use App\Enums\ExcelImportType;
use App\Enums\ImportEventStatus;
use App\Imports\FinalResultsImport;
use App\Models\ExcelImportEvent;
use Exception;
use Illuminate\Console\Command;
use Maatwebsite\Excel\HeadingRowImport;

final class UploadRawFinalResults extends Command
{
    protected $signature = 'rp:upload-raw-final-results';

    protected $description = 'Read uploaded Excel file and store in Raw Final Results';

    public function handle(): int
    {
        $importEvents = ExcelImportEvent::query()
            ->where('status', ImportEventStatus::QUEUED)
            ->get();

        if ($importEvents->isEmpty()) {
            return Command::SUCCESS;
        }

        $importEvents->toQuery()->update(['status' => ImportEventStatus::STARTED->value]);

        foreach ($importEvents as $event) {
            $event->updateStatus(ImportEventStatus::UPLOADING);

            $headings = (new HeadingRowImport())->toArray($event->file_path)[0][0];

            $validation = (new ValidateHeadings())->execute($headings, ExcelImportType::FINAL_RESULTS);

            try {
                FinalResultsImport::new($event, $validation['validated'])->import($event->file_path);
            } catch (Exception $e) {
                $event->setMessage($e->getMessage());

                continue;
            }

            $event->updateStatus(ImportEventStatus::UPLOADED);
        }

        return Command::SUCCESS;
    }
}
