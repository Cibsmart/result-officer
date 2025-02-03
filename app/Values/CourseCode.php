<?php
declare(strict_types=1);

namespace App\Values;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use InvalidArgumentException;

final readonly class CourseCode
{

    /**
     * @param string $string
     */
    public function __construct(public string $value)
    {
        $codes = Str::of($this->value)->explode('/');

        if (!$this->matchCodes($codes)) {
            throw new InvalidArgumentException('Invalid course code');
        }

//        if (!$this->matches(Str::trim($this->value))) {
//            throw new InvalidArgumentException('Invalid course code');
//        }
    }

    public static function new(string $code): self
    {
        return new self(Str::of($code)->trim()->upper()->value());
    }

    /** @param Collection<int, string> $codes */
    private function matchCodes(Collection $codes): bool
    {
        $passed = true;

        foreach ($codes as $code) {
            $passed = $this->matches($code) && $passed;
        }

        return $passed;
    }

    private function matches($code): bool
    {
        $cleanCode = $this->getCleanCode($code);

        $pattern = $this->allowsFourCharacterCode($cleanCode)
            ? '/^[a-z]{4} \d{3}[a-d]?$/i'
            : '/^[a-z]{3} \d{3}[a-d]?$/i';

        return (bool) preg_match($pattern, $cleanCode);
    }

    private function getCleanCode(string $code): string
    {
        if (Str::contains($code, 'EBSU')) {
            return Str::after($code, 'EBSU-');
        }

        return $code;
    }

    private function allowsFourCharacterCode(string $code): bool
    {
        return Str::contains($code, 'ARCH') || Str::contains($code, 'ARST');
    }
}