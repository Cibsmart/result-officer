<?php

declare(strict_types=1);

namespace App\Http\Controllers\Students;

use Illuminate\Foundation\Http\FormRequest;

final class StudentDeleteRequest extends FormRequest
{
    /**
     * phpcs:ignore SlevomatCodingStandard.Files.LineLength
     * @return array<string, array<int, \App\Rules\Updated|\Illuminate\Validation\Rules\Unique|\Illuminate\Validation\Rules\Date|string>>
     */
    public function rules(): array
    {
        return [

        ];
    }
}
