<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Imports\Excel\ValidateHeadings;
use App\Enums\ExcelImportType;
use App\Enums\ImportEventStatus;
use App\Imports\Excel\Spreadsheet;
use App\Models\ExcelImportEvent;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

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

        $filePath = Storage::path($importEvent->file_path);

        $validation = (new ValidateHeadings())->execute(Spreadsheet::headings($filePath), $type);

        $importEvent->updateStatus(ImportEventStatus::UPLOADING);

        try {
            $type->getImportClass()::new($importEvent, $validation['validated'])->import($filePath);
        } catch (Exception $e) {
            $importEvent->setMessage($e->getMessage());

            return Command::FAILURE;
        }

        if ($importEvent->rawRecordCount() === 0) {
            $importEvent->setMessage(
                'No rows were imported from the file. Confirm the registration number column is mapped '
                . 'correctly and that the file contains data rows.',
            );

            return Command::FAILURE;
        }

        $importEvent->updateStatus(ImportEventStatus::UPLOADED);

        return Command::SUCCESS;
    }
}
