<?php

declare(strict_types=1);

namespace App\Pipelines\Checks\ExcelImports\RawExcelResults;

use App\Enums\ChecklistType;
use App\Models\ExcelImportEvent;
use App\Values\TotalScore;
use Closure;
use Exception;

final class CheckTotal
{
    private string $type = ChecklistType::TOTAL->value;

    /**
     * @param array{event: 'App\Models\ExcelImportEvent', errors: array<string, string>} $data
     * @return array{event: 'App\Models\ExcelImportEvent', errors: array<string, string>}
     */
    public function handle(array $data, Closure $next): array
    {
        $messages = [];

        $event = $data['event'];
        assert($event instanceof ExcelImportEvent);

        $values = $event->rawExcelResults()->pluck($this->type)->unique();

        foreach ($values as $value) {
            try {
                TotalScore::new($value);
            } catch (Exception) {
                $messages[] = $value;
            }
        }

        if (count($messages) > 0) {
            $data['errors']["invalid_{$this->type}"] = $messages;
        }

        return $next($data);
    }
}
