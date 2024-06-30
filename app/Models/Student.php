<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\GenderEnum;
use Illuminate\Database\Eloquent\Model;

final class Student extends Model
{
    /** @var array<int, string> */
    protected $fillable = [
        'matriculation_number',
        'last_name',
        'first_name',
        'other_names',
        'gender',
        'date_of_birth',
        'country_id',
        'program_id',
        'entry_session_id',
        'entry_level_id',
        'entry_mode_id',
        'jamb_registration_number',
        'online_id',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'gender' => GenderEnum::class,
        ];
    }
}
