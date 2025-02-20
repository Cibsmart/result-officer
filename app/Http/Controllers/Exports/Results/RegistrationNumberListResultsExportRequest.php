<?php

declare(strict_types=1);

namespace App\Http\Controllers\Exports\Results;

use App\Values\RegistrationNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;

final class RegistrationNumberListResultsExportRequest extends FormRequest
{
    /** @return array<string, array<string>|string> */
    public function rules(): array
    {
        return ['registration_numbers' => ['required', 'string']];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator): void {

            $registrationNumbers = Str::of($this->registration_numbers)
                ->replace(' ', '')
                ->explode(',')
                ->filter()
                ->unique();

            $invalidNumbers = $this->validateRegistrationNumbers($registrationNumbers);

            if ($invalidNumbers->isEmpty()) {
                return;
            }

            $invalidNumbersText = $invalidNumbers->join(', ');
            $validator->errors()->add('registration_numbers',
                "The following registration numbers are invalid: {$invalidNumbersText}",
            );
        });
    }

    /**
     * @param \Illuminate\Support\Collection<int, non-falsy-string> $registrationNumbers
     * @return \Illuminate\Support\Collection<int, non-falsy-string>
     */
    private function validateRegistrationNumbers(Collection $registrationNumbers): Collection
    {
        return $registrationNumbers->filter(fn ($number) => ! RegistrationNumber::isValid($number));
    }
}
