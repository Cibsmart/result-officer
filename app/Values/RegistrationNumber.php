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
        if (! preg_match('/^ebsu\/\d{4}\/\d{4,6}[a-z]?$/i', Str::trim($this->value))) {
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

    public function session(): string
    {
        $year = $this->year();
        $next = $year + 1;

        return Str::of((string) $year)
            ->append('/')
            ->append((string) $next)
            ->value();
    }

    public function year(): int
    {
        $year = Str::of($this->value)->explode('/')[1];

        return (int) $year;
    }

    public function allowEGrade(): bool
    {
        return ! in_array($this->year(), [2013, 2014, 2015, 2016, 2017], true);
    }
}
