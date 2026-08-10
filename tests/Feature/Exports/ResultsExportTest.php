<?php

declare(strict_types=1);

use App\Exports\ResultsExport;
use App\Models\Lecturer;
use App\Models\Result;
use App\Models\Student;
use OpenSpout\Reader\XLSX\Reader;
use Tests\Factories\RegistrationFactory;
use Tests\Factories\ResultFactory;
use Tests\Factories\SemesterEnrollmentFactory;
use Tests\Factories\SemesterFactory;
use Tests\Factories\SessionEnrollmentFactory;
use Tests\Factories\SessionFactory;
use Tests\Factories\StudentFactory;

/**
 * Reads a written workbook back, first sheet only.
 * @return array<int, array<int, bool|\DateInterval|\DateTimeInterface|float|int|string|null>>
 */
function readWorkbook(string $path): array
{
    $reader = new Reader();
    $reader->open($path);

    $rows = [];

    foreach ($reader->getSheetIterator() as $sheet) {
        foreach ($sheet->getRowIterator() as $row) {
            $rows[] = $row->toArray();
        }

        break;
    }

    $reader->close();

    return $rows;
}

function studentWithResult(): Student
{
    $session = SessionFactory::new()->createOne(['name' => '2019/2020']);
    $semester = SemesterFactory::new(['name' => 'FIRST'])->createOne();

    $student = StudentFactory::new()->has(
        SessionEnrollmentFactory::new()->state(['session_id' => $session->id])
            ->has(SemesterEnrollmentFactory::new()
                ->has(RegistrationFactory::new()
                    ->has(ResultFactory::new()), 'registrations')
                ->state(['semester_id' => $semester->id]), 'semesterEnrollments'),
    )->createOne(['entry_session_id' => $session->id]);

    $lecturer = Lecturer::query()->create(['department' => 'MATHEMATICS', 'name' => 'DR EXAMINER']);

    Result::query()->update(['lecturer_id' => $lecturer->id]);

    return $student;
}

test('it writes a downloadable workbook with only the heading row when nothing matches', function (): void {
    $response = ResultsExport::forStudents([])->download('results.xlsx');

    $rows = readWorkbook($response->getFile()->getPathname());

    expect($response->headers->get('content-disposition'))->toContain('results.xlsx')
        ->and($rows)->toHaveCount(1)
        ->and($rows[0])->toHaveCount(24)
        ->and($rows[0][0])->toBe('SN')
        ->and($rows[0][1])->toBe('ID')
        ->and($rows[0][23])->toBe('Old Registration Number');
});

test('it writes a row per result, mapped onto the heading columns', function (): void {
    $student = studentWithResult();

    $result = Result::query()->firstOrFail();
    $registration = $result->registration;

    $rows = readWorkbook(
        ResultsExport::forStudents([$student->id])->download('results.xlsx')->getFile()->getPathname(),
    );

    expect($rows)->toHaveCount(2);

    $row = $rows[1];

    expect($row[0])->toBe('1')
        ->and($row[1])->toBe($registration->id)
        ->and($row[3])->toBe($student->registration_number)
        ->and($row[7])->toBe($result->total_score)
        ->and($row[8])->toBe($result->grade)
        ->and($row[10])->toBe('FIRST')
        ->and($row[11])->toBe('2019/2020')
        ->and($row[15])->toBe('DR EXAMINER')
        ->and($row[16])->toBe('MATHEMATICS');
});
