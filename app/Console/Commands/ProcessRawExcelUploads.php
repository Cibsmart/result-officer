<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\ExcelImportType;
use App\Enums\ImportEventStatus;
use App\Models\ExcelImportEvent;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Collection;

final class ProcessRawExcelUploads extends Command
{
    protected $signature = 'rp:process-raw-excel-uploads';

    protected $description = 'Process Raw Excel Uploads and Store in respective DB Tables one at a time';

    public function handle(): int
    {
        $importEvent = ExcelImportEvent::query()
            ->whereIn('status', [ImportEventStatus::UPLOADED, ImportEventStatus::REPROCESS])
            ->orderBy('id')
            ->first();

        if ($importEvent === null) {
            return Command::SUCCESS;
        }

        $importEvent->updateStatus(ImportEventStatus::PROCESSING);

        assert($importEvent instanceof ExcelImportEvent);

        $messages = collect($this->preprocess($importEvent))->filter();

        if ($messages->isNotEmpty()) {
            $importEvent->setMessage($this->joinMessages($messages));

            return Command::FAILURE;
        }

        $type = $importEvent->type;
        assert($type instanceof ExcelImportType);

        try {
            $type->getProcessAction()::new()->execute($importEvent);
        } catch (Exception $e) {
            $importEvent->updateStatus(ImportEventStatus::FAILED);
            $importEvent->setMessage($e->getMessage());

            return Command::FAILURE;
        }

        $importEvent->updateStatus(ImportEventStatus::COMPLETED);

        return Command::SUCCESS;
    }

    /** @return array<string, array<int, string>> */
    private function preprocess(ExcelImportEvent $event): array
    {
        $type = $event->type;

        return app(Pipeline::class)
            ->send(['event' => $event, 'errors' => []])
            ->through($type->getPreprocessChecks())
            ->thenReturn()['errors'];
    }

    /** @param \Illuminate\Support\Collection<string, non-empty-array<int, string>> $messages */
    private function joinMessages(Collection $messages): string
    {
        return $messages
            ->map(fn (array $value, string $key) => "{$key}: " . collect($value)->join(', '))
            ->join("\n");
    }
}
