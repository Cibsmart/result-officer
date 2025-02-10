<?php

declare(strict_types=1);

namespace App\Pipelines\Checks\ExcelImports\RawFinalResults;

use App\Enums\ChecklistType;
use App\Models\ExcelImportEvent;
use App\Models\Student;
use App\Values\RegistrationNumber;
use Closure;
use Exception;

final class CheckRegistrationNumber
{
    private string $type = ChecklistType::REGISTRATION_NUMBER->value;

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

        $dbRegistrationNumbers = Student::query()->whereIn($this->type, $values)->pluck($this->type);

        foreach ($values as $value) {
            try {
                RegistrationNumber::new($value);
            } catch (Exception) {
                $messages[] = $value;
            }
        }

        $diff = $values->diff($dbRegistrationNumbers);

        if (count($messages) > 0) {
            $data['errors']["invalid_{$this->type}"] = $messages;
        }

        if ($diff->isNotEmpty()) {
            $data['errors']["not_found_{$this->type}"] = $diff->toArray();
        }

        return $next($data);
    }
}
