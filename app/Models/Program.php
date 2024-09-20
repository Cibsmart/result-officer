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
        'Economics Education' => 'ECO',
        'English Language Education' => 'ENG',
        'History & Inter Relation Edu' => 'HIR',
        'Igbo Education' => 'IGB',
        'Political Science Education' => 'POL',
        'Religion Eduation ' => 'REL',
        'Social Study Education' => 'SS',

        'Accounting Education' => 'ACC',
        'Marketing Education' => 'MKT',
        'Office Technology' => 'OT',
        'Secrtarial Education' => 'SEC',

        'Admin and Planning' => 'AP',
        'Guidance Councelling' => 'GC',

        'Health Education' => 'HED',
        'HUMAN KINETICS' => 'HKE',
        'Physical Education' => 'PHY',

        'APPLIED STATISTICS' => 'STA',
        'INDUSTRIAL MATHEMATICS' => 'IMAT',
        'NONE' => '',
        'MATHEMATICS AND COMPUTER SCIENCE' => 'CSC',
        'PURE MATHEMATICS' => 'PMAT',

        'English Language and Literary Studies' => 'ENG',
        'French Linguistics' => 'FRE',
        'Language/Linguistics' => 'LIN',
        'Igbo Linguistics' => 'IGB',

        'LIS and Economics' => 'ECO',
        'LIS and English' => 'ENG',
        'LIS and French' => 'FRE',
        'LIS and History and International Relations' => 'HIR',
        'LIS and Language and Literature' => 'LIT',
        'LIS and Mass Communication' => 'MAC',
        'LIS and Political Science' => 'POL',
        'LIS and Psychology' => 'PSY',
        'LIS and Sociology/Anthropology' => 'SOC',

        'PHILOSOPHY' => 'PHIL',
        'RELIGION' => 'REL',

        'Psychology' => 'PSY',
        'Sociology' => 'SOC',

        'Biology Education' => 'BIO',
        'Chemistry Education' => 'CHM',
        'Computer science Education' => 'CSC',
        'Inter Science Education' => 'ISE',
        'Mathematics Education' => 'MAT',
        'Measurement And Evaluation' => 'MAE',
        'Physics Education' => 'PHY',

        'Education of the Gifted and Talented' => 'EGT',
        'Education of Hearing Impairment' => 'EHI',
        'Education of Learning Disabilities' => 'ELD',
        'Education of Visually Impaired' => 'EVI',

        'Film Production' => 'FP',
        'Theatre Arts' => 'TA',
        'Theatre and Film Production' => 'TFP',

        'Agric Science Education' => 'AGR',
        'Building  Education' => 'BLD',
        'Electrical / Electronics Edu' => 'EEE',
        'Mechanical Engr Education' => 'MEE',
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
            return Str::of($words->first())->take(3)->value();
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
