<?php

declare(strict_types=1);

namespace App\Queries;

use App\Data\Query\StudentCoursesData;
use App\Models\Student;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class StudentCourses
{
    private Builder $query;

    public function __construct(public Student $student)
    {
        $this->query = DB::table('students')
            ->join('session_enrollments', 'students.id', '=', 'session_enrollments.student_id')
            ->join('semester_enrollments', 'session_enrollments.id', '=', 'semester_enrollments.session_enrollment_id')
            ->join('registrations', 'semester_enrollments.id', '=', 'registrations.semester_enrollment_id')
            ->leftJoin('results', 'registrations.id', '=', 'results.registration_id')
            ->join('academic_sessions', 'session_enrollments.session_id', '=', 'academic_sessions.id')
            ->join('levels', 'session_enrollments.level_id', '=', 'levels.id')
            ->join('semesters', 'semester_enrollments.semester_id', '=', 'semesters.id')
            ->join('courses', 'registrations.course_id', '=', 'courses.id')
            ->select(
                'students.id', 'registrations.id as registration_id', 'academic_sessions.name as session',
                'semesters.name as semester', 'courses.id as course_id', 'courses.code as course_code',
                'courses.title as course_title', 'registrations.credit_unit', 'registrations.course_status',
                'results.total_score', 'results.grade', 'results.grade_point', 'academic_sessions.id as session_id',
                'program_curriculum_course_id',
            )
            ->where('students.id', $student->id)
            ->whereNull('students.deleted_at')
            ->orderBy('academic_sessions.name')
            ->orderBy('semesters.name')
            ->orderBy('courses.code');
    }

    public static function for(Student $student): self
    {
        return new self($student);
    }

    public function query(): Builder
    {
        return $this->query;
    }

    /** @return \Illuminate\Support\Collection<int, \App\Data\Query\StudentCoursesData> */
    public function get(): Collection
    {
        return StudentCoursesData::collect($this->query->get());
    }
}
