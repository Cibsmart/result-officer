<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Program extends Model
{
    use softDeletes;

    protected $fillable = ['department_id', 'code', 'name', 'program_type_id', 'online_id'];

    protected $with = ['department'];

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Department, \App\Models\Program> */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\ProgramType, \App\Models\Program> */
    public function programType(): BelongsTo
    {
        return $this->belongsTo(ProgramType::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Student> */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    /** @return \Illuminate\Database\Eloquent\Casts\Attribute<string, string> */
    public function name(): Attribute
    {
        return Attribute::make(
            /** @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter */
            get: fn (?string $value, array $attributes): string => $this->department->name === $attributes['name']
                    ? $attributes['name']
                    : "$this->department->name ({$attributes['name']})");
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'is_active' => 'bool',
        ];
    }
}
