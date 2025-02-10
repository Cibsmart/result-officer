<?php

declare(strict_types=1);

namespace App\Pipelines\Checks\ExcelImports\RawCurriculumCourses;

use App\Enums\ChecklistType;
use App\Enums\CourseType;
use App\Models\ExcelImportEvent;
use Closure;

final class CheckCourseType
{
    private string $type = ChecklistType::COURSE_TYPE->value;

    /**
     * @param array{event: 'App\Models\ExcelImportEvent', errors: array<string, string>} $data
     * @return array{event: 'App\Models\ExcelImportEvent', errors: array<string, string>}
     */
    public function handle(array $data, Closure $next): array
    {
        $messages = [];

        $event = $data['event'];
        assert($event instanceof ExcelImportEvent);

        $values = $event->rawCurriculumCourses()->pluck($this->type)->unique();

        foreach ($values as $value) {
            if (CourseType::fromNameOrCode($value) !== null) {
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
