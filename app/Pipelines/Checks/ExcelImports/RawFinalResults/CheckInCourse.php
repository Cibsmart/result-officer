<?php

declare(strict_types=1);

namespace App\Pipelines\Checks\ExcelImports\RawFinalResults;

use App\Enums\ChecklistType;
use App\Models\ExcelImportEvent;
use App\Values\InCourseScore;
use Closure;
use Exception;

final class CheckInCourse
{
    private string $type = ChecklistType::IN_COURSE->value;

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
            try {
                InCourseScore::new($value);
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
