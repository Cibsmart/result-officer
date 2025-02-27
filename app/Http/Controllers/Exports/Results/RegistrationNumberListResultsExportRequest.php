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
        return [
            'registration_numbers' => ['required'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator): void {

            $invalidNumbers = $this->validateRegistrationNumbers($validator->validated()['registration_numbers']);

            if ($invalidNumbers->isEmpty()) {
                return;
            }

            $message = "The following registration numbers are invalid: {$invalidNumbers->join(', ')}";

            $validator->errors()->add('registration_numbers', $message);
        });
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'registration_numbers' => Str::of($this->registration_numbers)
                ->replace("\n", ',')
                ->replace(' ', '')
                ->explode(',')
                ->filter()
                ->unique(),
        ]);
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
