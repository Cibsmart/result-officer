<?php

declare(strict_types=1);

use App\Enums\ExcelImportType;
use App\Enums\ImportEventStatus;
use App\Models\ExcelImportEvent;
use App\Models\RawCurriculumCourse;
use App\Models\RawExcelResult;
use Illuminate\Support\Facades\Storage;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer;
use Tests\Factories\UserFactory;

use function Pest\Laravel\artisan;

/**
 * @param array<int, string> $headings
 * @param array<int, array<int, int|string>> $rows
 */
function storeWorkbook(string $directory, array $headings, array $rows): string
{
    $path = $directory . '/' . uniqid('test_', true) . '.xlsx';

    Storage::disk('local')->makeDirectory($directory);

    $writer = new Writer();
    $writer->openToFile(Storage::disk('local')->path($path));
    $writer->addRow(Row::fromValues($headings));

    foreach ($rows as $row) {
        $writer->addRow(Row::fromValues($row));
    }

    $writer->close();

    return $path;
}

/** @param array<int, array<int, int|string>> $rows */
function storeResultsWorkbook(array $rows): string
{
    // Note: both registration_number and old_registration_number are present,
    // reproducing the RES4.xlsx collision that silently dropped every row.
    $headings = [
        'sn', 'students_name', 'registration_number', 'old_registration_number',
        'in_course_1', 'in_course_2', 'exam', 'total', 'grade', 'credit_unit',
        'semester', 'session', 'course_code', 'course_title', 'students_department',
        'examiners_name', 'examiners_department', 'exam_date',
    ];

    return storeWorkbook('result', $headings, $rows);
}

/** @param array<int, array<int, int|string>> $rows */
function storeCurriculumWorkbook(array $rows): string
{
    $headings = [
        'sn', 'program', 'curriculum', 'entry_mode', 'session', 'level', 'semester',
        'course_type', 'course_code', 'course_title', 'credit_unit',
        'minimum_elective_unit', 'minimum_elective_count', 'elective_group',
    ];

    return storeWorkbook('curriculum', $headings, $rows);
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
            UserFactory::new()->admin()->createOne(),
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
        UserFactory::new()->admin()->createOne(),
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

test('it imports curriculum courses and skips rows without a course code', function (): void {
    $path = storeCurriculumWorkbook([
        [1, 'MEDICAL LABORATORY SCIENCE', 'REGULAR', 'UTME', '2020/2021', '400', 'FIRST',
            'CORE', 'AMB 422', 'MEDICAL VIROLOGY', 3, 0, 0, ''],
        [2, 'MEDICAL LABORATORY SCIENCE', 'REGULAR', 'UTME', '2020/2021', '400', 'FIRST',
            'ELECTIVE', '', 'NO CODE', 2, 4, 2, 'GROUP A'],
    ]);

    $event = ExcelImportEvent::new(
        UserFactory::new()->admin()->createOne(),
        ExcelImportType::CURRICULUM,
        $path,
        'CUR1.xlsx',
    );

    artisan('rp:upload-pending-excel-imports')->assertExitCode(0);

    $event->refresh();

    $course = RawCurriculumCourse::query()->firstOrFail();

    expect($event->status)->toBe(ImportEventStatus::UPLOADED)
        ->and($event->rawCurriculumCourses()->count())->toBe(1)
        ->and($course->course_code)->toBe('AMB 422')
        ->and($course->credit_unit)->toBe(3)
        ->and($course->entry_session)->toBe('2020/2021')
        ->and($course->excel_import_event_id)->toBe($event->id)
        ->and($course->created_at)->not->toBeNull();

    Storage::disk('local')->delete($path);
});
