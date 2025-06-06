<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

final class Department extends Model
{
    use softDeletes;

    public static function createFromRawDepartment(RawDepartment $rawDepartment): self
    {
        $faculty = Faculty::getOrCreate($rawDepartment->faculty);

        $department = self::getOrCreate($rawDepartment, $faculty);

        Program::createForDepartment($department, $rawDepartment);

        return $department;
    }

    public static function getUsingOnlineId(string $departmentOnlineId): self
    {
        return
            Cache::remember("department_online_id.{$departmentOnlineId}",
                now()->addDay(),
                fn () => self::query()->where('online_id', $departmentOnlineId)->firstOrFail());
    }

    public static function getUsingId(int $departmentId): self
    {
        return
            Cache::remember("department_id.{$departmentId}",
                now()->addDay(),
                fn () => self::query()->where('id', $departmentId)->firstOrFail());
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Faculty, $this>
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Faculty, static>
     */
    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    /**
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Program, $this>
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Program, static>
     */
    public function programs(): HasMany
    {
        return $this->hasMany(Program::class);
    }

    /**
     * phpcs:ignore SlevomatCodingStandard.Files.LineLength
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\HasManyThrough<\App\Models\Student, \App\Models\Program, $this>
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough<\App\Models\Student, \App\Models\Program, static>
     */
    public function students(): HasManyThrough
    {
        return $this->hasManyThrough(Student::class, Program::class);
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
            set: static fn (string $value): string => mb_strtoupper(mb_trim($value)),
        );
    }

    /** @return \Illuminate\Database\Eloquent\Casts\Attribute<string, string> */
    protected function code(): Attribute
    {
        return Attribute::make(
            set: static fn (string $value): string => mb_strtoupper(mb_trim($value)),
        );
    }

    private static function getOrCreate(RawDepartment $rawDepartment, Faculty $faculty): self
    {
        $department = self::query()
            ->where('name', $rawDepartment->name)
            ->where('faculty_id', $faculty->id)
            ->first();

        if ($department) {
            return $department;
        }

        $department = new self();
        $department->name = $rawDepartment->name;
        $department->code = $rawDepartment->code;
        $department->faculty_id = $faculty->id;
        $department->online_id = $rawDepartment->online_id;
        $department->slug = Str::slug($rawDepartment->code);
        $department->save();

        return $department;
    }
}
