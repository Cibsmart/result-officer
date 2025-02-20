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
        if (! self::isValid($this->value)) {
            throw new InvalidArgumentException('Invalid registration number');
        }
    }

    public static function pattern(): string
    {
        return '/^(ebsu)\/(\d{4})\/(\d{3,7}[a-d]?)$/i';
    }

    public static function new(string $value): self
    {
        return new self(Str::upper($value));
    }

    public static function isValid(string $value): bool
    {
        return preg_match(self::pattern(), Str::trim($value), $matches) && (int) $matches[2] <= now()->year;
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
        preg_match(self::pattern(), $this->value, $matches);

        return (int) $matches[2];
    }

    public function number(): string
    {
        preg_match(self::pattern(), $this->value, $matches);

        return $matches[3];
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
