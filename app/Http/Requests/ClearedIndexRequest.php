<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\Months;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class ClearedIndexRequest extends FormRequest
{
    /** @return array<string, array<int, \Illuminate\Validation\Rules\Enum|string>> */
    public function rules(): array
    {
        return [
            'department' => ['required', 'integer', 'exists:departments,id'],
            'month' => ['required', Rule::enum(Months::class)],
            'year' => ['required', 'integer', 'regex:/^\d{4}$/'],
        ];
    }
}
