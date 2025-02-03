<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Institution;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class ProfileUpdateRequest extends FormRequest
{
    /** @return array<string, string|array<int, string|\Illuminate\Validation\Rules\Unique>> */
    public function rules(): array
    {
        $domain = Institution::firstOrFail()->domain;

        return [
            'email' => [
                'required', 'string', 'lowercase', 'email', 'max:255', "ends_with:@{$domain}",
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
//            'name' => ['required', 'string', 'max:255'],
        ];
    }
}
