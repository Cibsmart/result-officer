<?php

declare(strict_types=1);

namespace App\Exports;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use OpenSpout\Common\Entity\Cell;
use OpenSpout\Common\Entity\Comment\Comment;
use OpenSpout\Common\Entity\Comment\TextRun;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Options;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

final class ResultsExport
{
    private const string ID_COLUMN_NOTE
        = 'Do NOT edit values in this column. For any inserted record set the ID to 0';

    private const array HEADINGS = [
        'SN',
        'ID',
        'Students Name',
        'Registration Number',
        'In Course 1',
        'In Course 2',
        'Exam',
        'Total',
        'Grade',
        'Credit Unit',
        'Semester',
        'Session',
        'Course Code',
        'Course Title',
        'Students Department',
        'Examiners Name',
        'Examiners Department',
        'Exam Date',
        'Year',
        'Month',
        'Originating Session',
        'Database Officer',
        'Exam Officer',
        'Old Registration Number',
    ];

    /**
     * Openspout cannot auto size columns, so each one is given a width that fits
     * the values it holds. Indexed the same way as HEADINGS.
     */
    private const array COLUMN_WIDTHS = [
        6, 10, 34, 22, 12, 12, 8, 8, 8, 12, 12, 14, 14, 40, 30, 30, 30, 14, 8, 12, 20, 22, 22, 24,
    ];

    private int $rowNumber = 0;

    /** @param array<int, int> $studentIds */
    public function __construct(private readonly array $studentIds)
    {
    }

    /** @param array<int, int> $studentIds */
    public static function forStudents(array $studentIds): self
    {
        return new self($studentIds);
    }

    public function download(string $fileName): BinaryFileResponse
    {
        return response()->download($this->write(), $fileName)->deleteFileAfterSend();
    }

    public function query(): Builder
    {
        return DB::table('students')
            ->join('session_enrollments', 'students.id', '=', 'session_enrollments.student_id')
            ->join('semester_enrollments', 'session_enrollments.id', '=', 'semester_enrollments.session_enrollment_id')
            ->join('registrations', 'semester_enrollments.id', '=', 'registrations.semester_enrollment_id')
            ->leftJoin('results', 'registrations.id', '=', 'results.registration_id')
            ->join('academic_sessions', 'session_enrollments.session_id', '=', 'academic_sessions.id')
            ->join('levels', 'session_enrollments.level_id', '=', 'levels.id')
            ->join('semesters', 'semester_enrollments.semester_id', '=', 'semesters.id')
            ->join('courses', 'registrations.course_id', '=', 'courses.id')
            ->join('programs', 'students.program_id', '=', 'programs.id')
            ->join('departments', 'programs.department_id', '=', 'departments.id')
            ->join('lecturers', 'results.lecturer_id', '=', 'lecturers.id')
            ->select(
                'students.id', 'students.registration_number', 'students.last_name', 'students.first_name',
                'students.other_names', 'academic_sessions.name as session', 'semesters.name as semester',
                'courses.code as course_code', 'courses.title as course_title', 'registrations.credit_unit',
                'results.total_score', 'results.grade', 'results.exam_date', 'departments.name as department',
                'results.scores', 'programs.name as program', 'lecturers.name as examiner',
                'registrations.id as registration_id', 'lecturers.department as examiner_department',
                'students.old_registration_number',
            )
            ->whereIn('students.id', $this->studentIds)
            ->whereNull('students.deleted_at')
            ->whereNull('registrations.deleted_at')
            ->orderBy('students.registration_number')
            ->orderBy('academic_sessions.name')
            ->orderBy('semesters.name')
            ->orderBy('courses.code');
    }

    /**
     * The styles keyed by the column index they apply to.
     * @return array<int, \OpenSpout\Common\Entity\Style\Style>
     */
    private static function columnStyles(): array
    {
        $scoreStyle = (new Style())->setFormat('00');

        return [
            1 => (new Style())->setFontColor(Color::DARK_RED),
            4 => $scoreStyle,
            5 => $scoreStyle,
            6 => $scoreStyle,
            7 => $scoreStyle,
            17 => (new Style())->setFormat('@'),
        ];
    }

    private static function headingRow(): Row
    {
        $row = Row::fromValues(self::HEADINGS, (new Style())->setFontBold());

        $idHeading = $row->getCellAtIndex(1);
        assert($idHeading instanceof Cell);

        $idHeading->setStyle((new Style())->setFontColor(Color::DARK_RED));

        $comment = new Comment();
        $comment->addTextRun(new TextRun(self::ID_COLUMN_NOTE));

        $idHeading->comment = $comment;

        return $row;
    }

    /** Writes the workbook to a temporary file and returns its path. */
    private function write(): string
    {
        $path = tempnam(sys_get_temp_dir(), 'results-export-');
        assert(is_string($path));

        $options = new Options();

        foreach (self::COLUMN_WIDTHS as $index => $width) {
            $options->setColumnWidth($width, $index + 1);
        }

        $writer = new Writer($options);
        $writer->openToFile($path);
        $writer->addRow(self::headingRow());

        $columnStyles = self::columnStyles();

        foreach ($this->query()->cursor() as $record) {
            $writer->addRow(Row::fromValuesWithStyles($this->map($record), null, $columnStyles));
        }

        $writer->close();

        return $path;
    }

    /** @return array<int, int|string|null> */
    private function map(mixed $record): array
    {
        $this->rowNumber ++;

        $department = $record->department === $record->program
            ? $record->department
            : "{$record->department} ({$record->program})";

        $scores = json_decode((string) $record->scores);
        $inCourse2 = $scores->in_course_2 ?? '0';

        return [
            (string) $this->rowNumber,
            $record->registration_id,
            "{$record->last_name} {$record->first_name} {$record->other_names}",
            $record->registration_number,
            $scores->in_course,
            $inCourse2,
            $scores->exam,
            $record->total_score,
            $record->grade,
            $record->credit_unit,
            $record->semester,
            $record->session,
            $record->course_code,
            $record->course_title,
            $department,
            $record->examiner,
            $record->examiner_department,
            $record->exam_date,
            '',
            '',
            $record->session,
            '',
            '',
            $record->old_registration_number,
        ];
    }
}
