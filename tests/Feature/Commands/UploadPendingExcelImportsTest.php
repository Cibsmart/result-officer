<?php

declare(strict_types=1);

use App\Enums\ExcelImportType;
use App\Enums\ImportEventStatus;
use App\Models\ExcelImportEvent;
use App\Models\RawExcelResult;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Facades\Excel;
use Tests\Factories\UserFactory;

use function Pest\Laravel\artisan;

/** @param array<int, array<int, int|string>> $rows */
function storeResultsWorkbook(array $rows): string
{
    $export = new class($rows) implements FromArray, WithHeadings
    {
        /** @param array<int, array<int, int|string>> $rows */
        public function __construct(private readonly array $rows)
        {
        }

        /** @return array<int, string> */
        public function headings(): array
        {
            // Note: both registration_number and old_registration_number are present,
            // reproducing the RES4.xlsx collision that silently dropped every row.
            return [
                'sn', 'students_name', 'registration_number', 'old_registration_number',
                'in_course_1', 'in_course_2', 'exam', 'total', 'grade', 'credit_unit',
                'semester', 'session', 'course_code', 'course_title', 'students_department',
                'examiners_name', 'examiners_department', 'exam_date',
            ];
        }

        /** @return array<int, array<int, int|string>> */
        public function array(): array
        {
            return $this->rows;
        }
    };

    $path = 'result/' . uniqid('test_', true) . '.xlsx';
    Excel::store($export, $path, 'local');

    return $path;
}

/** @return array<int, int|string> A single results row matching storeResultsWorkbook()'s headings. */
function resultRow(string $registrationNumber): array
{
    return [
        1, 'JOHN DOE', $registrationNumber, '', 8, 0, 40, 48, 'A', 3,
        'FIRST', '2024/2025', 'AMB 422', 'MED VIRO', 'AMB', 'MR X', 'AMB', '16-9-2025',
    ];
}

test(
    'it imports rows and maps registration number correctly despite an old_registration_number column',
    function (): void {
        $path = storeResultsWorkbook([resultRow('EBSU/2020/0001')]);

        $event = ExcelImportEvent::new(
            UserFactory::new()->createOne(),
            ExcelImportType::RESULT,
            $path,
            'RES4.xlsx',
        );

        artisan('rp:upload-pending-excel-imports')->assertExitCode(0);

        $event->refresh();

        expect($event->status)->toBe(ImportEventStatus::UPLOADED)
            ->and($event->rawExcelResults()->count())->toBe(1)
            ->and(RawExcelResult::query()->value('registration_number'))->toBe('EBSU/2020/0001');

        Storage::disk('local')->delete($path);
    },
);

test('it fails loudly and does not complete when no rows are imported', function (): void {
    // A data row with an empty registration_number is skipped by the importer,
    // which previously sailed through to "completed" with zero data.
    $path = storeResultsWorkbook([resultRow('')]);

    $event = ExcelImportEvent::new(
        UserFactory::new()->createOne(),
        ExcelImportType::RESULT,
        $path,
        'EMPTY.xlsx',
    );

    artisan('rp:upload-pending-excel-imports')->assertExitCode(1);

    $event->refresh();

    expect($event->status)->toBe(ImportEventStatus::FAILED)
        ->and($event->rawExcelResults()->count())->toBe(0)
        ->and($event->message)->toContain('No rows were imported');

    Storage::disk('local')->delete($path);
});
