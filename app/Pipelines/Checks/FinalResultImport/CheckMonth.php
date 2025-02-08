<?php

declare(strict_types=1);

namespace App\Pipelines\Checks\FinalResultImport;

use App\Enums\ChecklistType;
use App\Enums\Months;
use App\Models\ExcelImportEvent;
use Closure;

final class CheckMonth
{
    private string $type = ChecklistType::MONTH->value;

    /**
     * @param array{event: 'App\Models\ExcelImportEvent', errors: array<string, string>} $data
     * @return array{event: 'App\Models\ExcelImportEvent', errors: array<string, string>}
     */
    public function handle(array $data, Closure $next): array
    {
        $messages = [];

        $event = $data['event'];
        assert($event instanceof ExcelImportEvent);

        $values = $event->rawFinalResults()->pluck($this->type)->unique();

        foreach ($values as $value) {
            if (Months::fromName($value) !== null) {
                continue;
            }

            $messages[] = $value;
        }

        if (count($messages) > 0) {
            $data['errors']["invalid_{$this->type}"] = $messages;
        }

        return $next($data);
    }
}
