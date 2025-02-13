<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\ExcelImportType;
use App\Enums\ImportEventStatus;
use App\Models\ExcelImportEvent;
use Illuminate\Console\Command;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Collection;

final class ProcessRawExcelUploads extends Command
{
    protected $signature = 'rp:process-raw-excel-uploads';

    protected $description = 'Process Raw Excel Uploads and Store in respective DB Tables';

    public function handle(): int
    {
        $importEvents = ExcelImportEvent::query()
            ->where('status', ImportEventStatus::UPLOADED)
            ->get();

        if ($importEvents->isEmpty()) {
            return Command::SUCCESS;
        }

        $importEvents->toQuery()->update(['status' => ImportEventStatus::PROCESSING->value]);

        foreach ($importEvents as $event) {
            assert($event instanceof ExcelImportEvent);

            $messages = collect($this->preprocess($event))->filter();

            if ($messages->isNotEmpty()) {
                $event->setMessage($this->joinMessages($messages));

                continue;
            }

            $type = $event->type;
            assert($type instanceof ExcelImportType);

            $type->getProcessAction()::new()->execute($event);

            $event->updateStatus(ImportEventStatus::COMPLETED);
        }

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
