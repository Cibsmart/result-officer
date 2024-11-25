<?php

declare(strict_types=1);

namespace App\Queries;

use App\Data\Query\ProgramCoursesData;
use App\Models\ProgramCurriculum;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class ProgramCourses
{
    private Builder $query;

    public function __construct(public ProgramCurriculum $programCurriculum)
    {
        $this->query = DB::table('program_curricula')
            ->join('program_curriculum_levels', 'program_curricula.id',
                '=', 'program_curriculum_levels.program_curriculum_id',
            )
            ->join('program_curriculum_semesters', 'program_curriculum_levels.id',
                '=', 'program_curriculum_semesters.program_curriculum_level_id',
            )
            ->join('program_curriculum_courses', 'program_curriculum_semesters.id',
                '=', 'program_curriculum_courses.program_curriculum_semester_id',
            )
            ->join('courses', 'program_curriculum_courses.course_id', '=', 'courses.id')
            ->join('semesters', 'program_curriculum_semesters.semester_id', '=', 'semesters.id')
            ->join('levels', 'program_curriculum_levels.level_id', '=', 'levels.id')
            ->join('academic_sessions', 'program_curricula.entry_session_id', '=', 'academic_sessions.id')
            ->select(
                'program_curricula.id', 'academic_sessions.name as session', 'levels.name as level',
                'semesters.name as semester', 'program_curriculum_semesters.minimum_elective_units',
                'courses.id as course_id', 'courses.code as course_code', 'courses.title as course_title',
                'program_curriculum_courses.course_type', 'program_curriculum_courses.credit_unit',
                'academic_sessions.id as session_id', 'levels.id as level_id',
                'program_curriculum_semesters.minimum_elective_count',
                'program_curriculum_courses.id as program_course_id',
            )
            ->where('program_curricula.id', $this->programCurriculum->id)
            ->orderBy('academic_sessions.name')
            ->orderBy('semesters.name')
            ->orderBy('courses.code');
    }

    public static function for(ProgramCurriculum $programCurriculum): self
    {
        return new self($programCurriculum);
    }

    public function query(): Builder
    {
        return $this->query;
    }

    /** @return \Illuminate\Support\Collection<int, \App\Data\Query\ProgramCoursesData> */
    public function get(): Collection
    {
        return ProgramCoursesData::collect($this->query->get());
    }
}
