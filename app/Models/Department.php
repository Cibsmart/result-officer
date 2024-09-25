<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Department extends Model
{
    use softDeletes;

    protected $fillable = ['faculty_id', 'code', 'name', 'online_id'];

    public static function createFromRawDepartment(RawDepartment $rawDepartment): void
    {
        $faculty = Faculty::getOrCreate($rawDepartment->faculty);

        $department = self::getOrCreate($rawDepartment, $faculty);

        Program::createForDepartment($department, $rawDepartment);
    }

    public static function getUsingOnlineId(string $departmentOnlineId): self
    {
        return self::query()->where('online_id', $departmentOnlineId)->firstOrFail();
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Faculty, \App\Models\Department> */
    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Program> */
    public function programs(): HasMany
    {
        return $this->hasMany(Program::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasManyThrough<\App\Models\Student> */
    public function students(): HasManyThrough
    {
        return $this->hasManyThrough(Student::class, Program::class);
    }

    private static function getOrCreate(RawDepartment $rawDepartment, Faculty $faculty): self
    {
        return self::firstOrCreate(
            ['name' => $rawDepartment->name],
            [
                'code' => $rawDepartment->code,
                'faculty_id' => $faculty->id,
                'online_id' => $rawDepartment->online_id,
            ],
        );
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'is_active' => 'bool',
        ];
    }

    /** @return \Illuminate\Database\Eloquent\Casts\Attribute<string, string> */
    protected function name(): Attribute
    {
        return Attribute::make(
            set: static fn (string $value): string => strtoupper(trim($value)),
        );
    }

    /** @return \Illuminate\Database\Eloquent\Casts\Attribute<string, string> */
    protected function code(): Attribute
    {
        return Attribute::make(
            set: static fn (string $value): string => strtoupper(trim($value)),
        );
    }
}
