<?php

declare(strict_types=1);

namespace App\Pipelines\Checks\ExcelImports\RawCurriculumCourses;

use App\Enums\ChecklistType;
use App\Models\ExcelImportEvent;
use App\Values\CourseCode;
use Closure;
use Exception;

final class CheckCourseCode
{
    private string $type = ChecklistType::COURSE_CODE->value;

    /**
     * @param array{event: 'App\Models\ExcelImportEvent', errors: array<string, string>} $data
     * @return array{event: 'App\Models\ExcelImportEvent', errors: array<string, string>}
     */
    public function handle(array $data, Closure $next): array
    {
        $messages = [];

        $event = $data['event'];
        assert($event instanceof ExcelImportEvent);

        $values = $event->rawProgramCurricula()->pluck($this->type)->unique();

        foreach ($values as $value) {
            try {
                CourseCode::new($value);
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
