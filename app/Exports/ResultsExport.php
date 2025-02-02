<?php

declare(strict_types=1);

namespace App\Exports;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

final readonly class ResultsExport implements FromQuery, ShouldAutoSize, WithEvents, WithHeadings, WithMapping
{
    use Exportable;

    /** @param array<int, int> $studentIds */
    public function __construct(private array $studentIds)
    {
    }

    /** @param array<int, int> $studentIds */
    public static function forStudents(array $studentIds): self
    {
        return new self($studentIds);
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
            )
            ->whereIn('students.id', $this->studentIds)
            ->whereNull('students.deleted_at')
            ->whereNull('registrations.deleted_at')
            ->orderBy('students.registration_number')
            ->orderBy('academic_sessions.name')
            ->orderBy('semesters.name')
            ->orderBy('courses.code');
    }

    /** @return array<int, string> */
    public function headings(): array
    {
        return [
            '#',
            'Name',
            'Registration Number',
            'In Course',
            'Exam',
            'Total Score',
            'Grade',
            'Course Code',
            'Course Title',
            'Department',
            'Examiner',
            'Exam Date',
        ];
    }

    /** @return array<int, string> */
    public function map(mixed $row): array
    {
        $department = $row->department === $row->program
            ? $row->department
            : "{$row->department} ({$row->program})";

        $scores = json_decode($row->scores);

        return [
            $row->id,
            "{$row->last_name} {$row->first_name} {$row->other_names}",
            $row->registration_number,
            $scores->in_course,
            $scores->exam,
            $row->total_score,
            $row->grade,
            $row->course_code,
            $row->course_title,
            $department,
            $row->examiner,
            $row->exam_date,
        ];
    }

    /** @return array<string, \Closure> */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event): void {
                $sheet = $event->getSheet();

                $sheet->getStyle('A1:L1')->getFont()->setBold(true);

                $sheet->formatColumn('D', '00');
                $sheet->formatColumn('E', '00');
                $sheet->formatColumn('F', '00');
                $sheet->formatColumn('L', NumberFormat::FORMAT_TEXT);
            },
        ];
    }
}
