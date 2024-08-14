<?php

declare(strict_types=1);

namespace App\Values;

use App\Models\Student;
use Exception;
use Illuminate\Support\Str;
use InvalidArgumentException;

final readonly class RegistrationNumber
{
    public function __construct(public string $value)
    {
        if (! preg_match('/^EBSU\/\d{4}\/\d{4,6}[A-Z]?$/i', Str::trim($this->value))) {
            throw new InvalidArgumentException('Invalid registration number');
        }
    }

    public static function new(string $value): self
    {
        return new self($value);
    }

    /** @throws \Exception */
    public function student(): Student
    {
        $student = Student::query()->where('registration_number', $this->value)->first();

        if (is_null($student)) {
            throw new Exception('STUDENT NOT FOUND: Download Student Records and Try Again');
        }

        return $student;
    }
}
