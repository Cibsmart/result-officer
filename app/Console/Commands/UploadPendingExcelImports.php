<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Imports\Excel\ValidateHeadings;
use App\Enums\ExcelImportType;
use App\Enums\ImportEventStatus;
use App\Models\ExcelImportEvent;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Maatwebsite\Excel\HeadingRowImport;

final class UploadPendingExcelImports extends Command
{
    protected $signature = 'rp:upload-pending-excel-imports';

    protected $description = 'Read uploaded Excel file and store in the respective raw db table';

    public function handle(): int
    {
        $importEvents = ExcelImportEvent::query()
            ->where('status', ImportEventStatus::QUEUED)
            ->get();

        if ($importEvents->isEmpty()) {
            return Command::SUCCESS;
        }

        ExcelImportEvent::updateStatues($importEvents, ImportEventStatus::STARTED);

        sleep(5);

        foreach ($importEvents as $event) {
            $event->updateStatus(ImportEventStatus::UPLOADING);

            $type = $event->type;
            assert($type instanceof ExcelImportType);

            $headings = (new HeadingRowImport())->toArray($event->file_path)[0][0];

            $validation = (new ValidateHeadings())->execute($headings, $type);

            try {
                $type->getImportClass()::new($event, $validation['validated'])->import($event->file_path);
            } catch (Exception $e) {
                $event->setMessage($e->getMessage());

                continue;
            }

            $event->updateStatus(ImportEventStatus::UPLOADED);
        }

        sleep(5);

        Artisan::call('rp:process-raw-excel-uploads');

        return Command::SUCCESS;
    }
}
