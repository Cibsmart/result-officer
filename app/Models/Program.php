<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

final class Program extends Model
{
    use softDeletes;

    private const CODES = [
        'Accounting Education' => 'ACC',
        'Admin and Planning' => 'AP',
        'Agric Science Education' => 'AGR',
        'APPLIED STATISTICS' => 'STA',
        'Biology Education' => 'BIO',
        'Building  Education' => 'BLD',
        'Chemistry Education' => 'CHM',
        'Computer science Education' => 'CSC',
        'Economics Education' => 'ECO',
        'Education of Hearing Impairment' => 'EHI',
        'Education of Learning Disabilities' => 'ELD',
        'Education of the Gifted and Talented' => 'EGT',
        'Education of Visually Impaired' => 'EVI',
        'Electrical / Electronics Edu' => 'EEE',
        'English Language and Literary Studies' => 'ENG',
        'English Language Education' => 'ENG',
        'Film Production' => 'FP',
        'French Linguistics' => 'FRE',
        'Guidance Councelling' => 'GC',
        'Health Education' => 'HED',
        'History & Inter Relation Edu' => 'HIR',
        'HUMAN KINETICS' => 'HKE',
        'Igbo Education' => 'IGB',
        'Igbo Linguistics' => 'IGB',
        'INDUSTRIAL MATHEMATICS' => 'IMAT',
        'Inter Science Education' => 'ISE',
        'Language/Linguistics' => 'LIN',
        'LIS and Economics' => 'ECO',
        'LIS and English' => 'ENG',
        'LIS and French' => 'FRE',
        'LIS and History and International Relations' => 'HIR',
        'LIS and Language and Literature' => 'LIT',
        'LIS and Mass Communication' => 'MAC',
        'LIS and Political Science' => 'POL',
        'LIS and Psychology' => 'PSY',
        'LIS and Sociology/Anthropology' => 'SOC',
        'Marketing Education' => 'MKT',
        'MATHEMATICS AND COMPUTER SCIENCE' => 'CSC',
        'Mathematics Education' => 'MAT',
        'Measurement And Evaluation' => 'MAE',
        'Mechanical Engr Education' => 'MEE',
        'Office Technology' => 'OT',
        'PHILOSOPHY' => 'PHIL',
        'Physical Education' => 'PHY',
        'Physics Education' => 'PHY',
        'Political Science Education' => 'POL',
        'Psychology' => 'PSY',
        'PURE MATHEMATICS' => 'PMAT',
        'RELIGION' => 'REL',
        'Religion Eduation ' => 'REL',
        'Secrtarial Education' => 'SEC',
        'Social Study Education' => 'SS',
        'Sociology' => 'SOC',
        'Theatre and Film Production' => 'TFP',
        'Theatre Arts' => 'TA',
        'Vocational Education' => 'VE',
    ];

    protected $fillable = ['department_id', 'code', 'name', 'program_type_id', 'online_id'];

    protected $with = ['department'];

    public static function createForDepartment(Department $department, RawDepartment $rawDepartment): void
    {
        $programs = $rawDepartment->options;

        if (count($programs) === 0) {
            self::new($department, $department->name, $department->code);

            return;
        }

        foreach ($programs as $program) {
            if (strtolower($program) === 'none') {
                continue;
            }

            $programCode = $department->code . '-' . self::getCode($program);

            self::new($department, $program, $programCode);
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

    public static function getFromDepartmentAndName(Department $department, string $programName): self
    {
        $programName = $programName !== ''
            ? $programName
            : $department->name;

        return $department->programs()->where('name', $programName)->firstOrFail();
    }

    public static function getUsingName(string $programName): self
    {
        return self::query()->where('name', $programName)->firstOrFail();
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

    private static function getCode(string $program): string
    {
        if (array_key_exists($program, self::CODES)) {
            return self::CODES[$program];
        }

        $words = Str::of($program)
            ->upper()
            ->remove(['LIS', 'OF', 'AND', 'THE', '&'])
            ->explode(' ')
            ->filter();

        if (count($words) === 1) {
            return str($words->first())->take(3)->value();
        }

        return $words
            ->map(fn ($word) => $word[0])
            ->join('');
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
