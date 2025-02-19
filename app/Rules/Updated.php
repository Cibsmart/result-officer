<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final readonly class Updated implements ValidationRule
{
    public function __construct(private string $originalValue)
    {
    }

    /** @param \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString $fail */
    public function validate(
        string $attribute,
        mixed $value,
        Closure $fail,
    ): void {
        if ($value !== $this->originalValue && $attribute !== '') {
            return;
        }

        $fail('The :attribute must be different from the previous value.');
    }
}
