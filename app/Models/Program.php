<?php

declare(strict_types=1);

namespace App\Models;

use App\Data\Download\PortalProgramData;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

final class Program extends Model
{
    use softDeletes;

    protected $fillable = ['department_id', 'code', 'name', 'program_type_id', 'online_id'];

    protected $with = ['department'];

    public static function createForDepartment(Department $department, RawDepartment $rawDepartment): void
    {
        $programs = PortalProgramData::collect($rawDepartment->options);

        if (count($programs) === 0) {
            self::new($department, $department->name, $department->code);

            return;
        }

        foreach ($programs as $program) {
            $programCode = Str::of($program->name)->limit(3, '')->value();

            self::new($department, $program->name, $programCode);
        }
    }

    public static function new(
        Department $department,
        string $programName,
        string $programCode,
    ): void {
        self::firstOrCreate(
            ['name' => $programName],
            [
                'code' => $programCode,
                'department_id' => $department->id,
                'program_type_id' => 5,
            ],
        );
    }

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
            get: fn (string $value): string => $this->department->name === $value
                ? $value
                : "{$this->department->name} ({$value})",

            set: fn (string $value): string => strtoupper(trim($value)),
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
    protected function code(): Attribute
    {
        return Attribute::make(
            set: static fn (string $value): string => strtoupper(trim($value)),
        );
    }
}
