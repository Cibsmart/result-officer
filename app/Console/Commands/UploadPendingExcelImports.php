<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Imports\Excel\ValidateHeadings;
use App\Enums\ExcelImportType;
use App\Enums\ImportEventStatus;
use App\Models\ExcelImportEvent;
use Exception;
use Illuminate\Console\Command;
use Maatwebsite\Excel\HeadingRowImport;

final class UploadPendingExcelImports extends Command
{
    protected $signature = 'rp:upload-pending-excel-imports';

    protected $description = 'Read uploaded Excel file and store in the respective raw db table';

    public function handle(): int
    {
        $importEvent = ExcelImportEvent::query()
            ->where('status', ImportEventStatus::QUEUED)
            ->orderBy('id')
            ->first();

        if ($importEvent === null) {
            return Command::SUCCESS;
        }

        $importEvent->updateStatus(ImportEventStatus::STARTED);

        $type = $importEvent->type;
        assert($type instanceof ExcelImportType);

        $headings = (new HeadingRowImport())->toArray($importEvent->file_path)[0][0];

        $validation = (new ValidateHeadings())->execute($headings, $type);

        $importEvent->updateStatus(ImportEventStatus::UPLOADING);

        try {
            $type->getImportClass()::new($importEvent, $validation['validated'])->import($importEvent->file_path);
        } catch (Exception $e) {
            $importEvent->setMessage($e->getMessage());

            return Command::FAILURE;
        }

        $importEvent->updateStatus(ImportEventStatus::UPLOADED);

        return Command::SUCCESS;
    }
}
