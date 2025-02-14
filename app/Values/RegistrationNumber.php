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
        if (! preg_match('/^ebsu\/\d{4}\/\d{3,7}[a-z]?$/i', Str::trim($this->value))) {
            throw new InvalidArgumentException('Invalid registration number');
        }
    }

    public static function new(string $value): self
    {
        return new self(Str::upper($value));
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
            ->append('-')
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
        return ! in_array($this->year(), [2_011, 2_012, 2_013, 2_014, 2_015, 2_016, 2_017], true);
    }

    public function slug(): string
    {
        return Str::of($this->value)->replace('/', '-')->slug()->value();
    }
}
