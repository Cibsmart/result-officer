<?php

declare(strict_types=1);

namespace App\Pipelines\Checks\FinalResultImport;

use App\Enums\ChecklistType;
use App\Models\ExcelImportEvent;
use Closure;

final class CheckYear
{
    private string $type = ChecklistType::YEAR->value;

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
            if (preg_match('/^20\d{2}$/i', $value) && $value <= now()->addYear()->year) {
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
