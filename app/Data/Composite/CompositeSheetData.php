<?php

declare(strict_types=1);

namespace App\Data\Composite;

use App\Data\Faculty\FacultyData;
use App\Data\Level\LevelData;
use App\Data\Program\ProgramData;
use App\Data\Results\ResultData;
use App\Data\Results\SemesterResultData;
use App\Data\Semester\SemesterData;
use App\Data\Session\SessionData;
use App\Enums\EntryMode;
use App\Models\Level;
use App\Models\Program;
use App\Models\ProgramCurriculum;
use App\Models\ProgramCurriculumCourse;
use App\Models\Semester;
use App\Models\Session;
use App\Models\Student;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Str;
use Spatie\LaravelData\Data;

final class CompositeSheetData extends Data
{
    public function __construct(
        public readonly ProgramData $program,
        public readonly FacultyData $faculty,
        public readonly SessionData $session,
        public readonly SemesterData $semester,
        public readonly LevelData $level,
        /** @var \Illuminate\Support\Collection<int, \App\Data\Composite\CompositeCourseListData> */
        public readonly Collection $courses,
        /** @var \Illuminate\Support\Collection<int, \App\Data\Composite\CompositeRowData> */
        public readonly Collection $students,
        public readonly bool $hasOtherCourses,
    ) {
    }

    public static function fromModel(
        Program $program,
        Session $session,
        Level $level,
        Semester $semester,
    ): self {
        $courses = self::getCourses($program, $session, $level, $semester);

        $programStudents = self::getProgramStudents($program, $session, $level, $semester);

        $students = self::prepareCompositeRows($programStudents, $session, $semester, $courses);

        return new self(
            program: ProgramData::fromModel($program),
            faculty: FacultyData::fromProgram($program),
            session: SessionData::fromModel($session),
            semester: SemesterData::fromModel($semester),
            level: LevelData::fromModel($level),
            courses: CompositeCourseListData::collect($courses),
            students: $students,
            hasOtherCourses: $students->contains(fn (CompositeRowData $student): bool => $student->otherCourses !== ''),
        );
    }

    /**
     * @param \Illuminate\Support\LazyCollection<int, \App\Models\Student> $students
     * @param \Illuminate\Support\Collection<int, array{code: string, unit: int}> $courses
     * @return \Illuminate\Support\Collection<int, \App\Data\Composite\CompositeRowData>
     */
    private static function prepareCompositeRows(
        LazyCollection $students,
        Session $session,
        Semester $semester,
        Collection $courses,
    ): Collection {
        $crossTab = [];

        foreach ($students as $student) {
            $courseList = $courses->pluck('unit', 'code')
                ->map(fn () => ['code' => '', 'grade' => '-', 'score' => '-'])->all();

            $semesterResultData = self::getSemesterResultDataForStudent($student, $session, $semester);

            [$levelCourses, $otherCourses, $failed] = self::prepareCompositeCourses($semesterResultData, $courseList);

            $crossTab[] = [
                'creditUnitTotal' => $semesterResultData->formattedCreditUnitTotal,
                'gradePointAverage' => $semesterResultData->formattedGPA,
                'gradePointTotal' => $semesterResultData->formattedGradePointTotal,
                'id' => $student->id,
                'levelCourses' => $levelCourses,
                'name' => $student->name,
                'otherCourses' => $otherCourses,
                'registrationNumber' => $student->registration_number,
                'remark' => $failed === '' ? 'PASS' : "FAIL: $failed",
            ];
        }

        return CompositeRowData::collect(collect($crossTab));
    }

    /** @return \Illuminate\Support\Collection<int, array{code: string, unit: int}> */
    private static function getCourses(
        Program $program,
        Session $session,
        Level $level,
        Semester $semester,
    ): Collection {

        $entrySession = $session->entry($level);

        $curriculum = ProgramCurriculum::query()
            ->where('program_id', $program->id)
            ->where('entry_session_id', $entrySession->id)
            ->where('entry_mode', EntryMode::UTME)
            ->first();

        if ($curriculum === null) {
            return collect([]);
        }

        $curriculumLevel = $curriculum->programCurriculumLevels()->where('level_id', $level->id)->first();
        $curriculumSemester = $curriculumLevel->programCurriculumSemesters()
            ->where('semester_id', $semester->id)
            ->first();

        return $curriculumSemester
            ->programCurriculumCourses()
            ->select('program_curriculum_courses.*')
            ->join('courses', 'courses.id', '=', 'program_curriculum_courses.course_id')
            ->orderBy('courses.code')
            ->with('course')
            ->get()
            ->map(
                fn (ProgramCurriculumCourse $course) => [
                    'code' => $course->course->code, 'unit' => $course->credit_unit->value,
                ],
            );
    }

    /** @return \Illuminate\Support\LazyCollection<int, \App\Models\Student> */
    private static function getProgramStudents(
        Program $program,
        Session $session,
        Level $level,
        Semester $semester,
    ): LazyCollection {
        return $program->students()
            ->with([
                'sessionEnrollments.semesterEnrollments.registrations.course',
                'sessionEnrollments.semesterEnrollments.registrations.result',
                'sessionEnrollments.semesterEnrollments.semester',
            ])
            ->whereHas('sessionEnrollments.semesterEnrollments',
                function (Builder $query) use ($session, $level, $semester): void {
                    $query->where('session_id', $session->id)
                        ->where('level_id', $level->id)
                        ->where('semester_id', $semester->id);
                })
            ->orderBy('registration_number')
            ->lazy();
    }

    private static function getSemesterResultDataForStudent(
        Student $student,
        Session $session,
        Semester $semester,
    ): SemesterResultData {
        $sessionEnrollment = $student->sessionEnrollments
            ->where('session_id', '===', $session->id)
            ->firstOrFail();

        $semesterEnrollment = $sessionEnrollment
            ->semesterEnrollments
            ->where('semester_id', '===', $semester->id)
            ->firstOrFail();

        return SemesterResultData::fromModel($semesterEnrollment);
    }

    /**
     * @param array<string, array<string, string>> $courseList
     * @return array{\Illuminate\Support\Collection<int, \App\Data\Composite\CompositeCourseData>, string, string}
     */
    private static function prepareCompositeCourses(SemesterResultData $semesterResultData, array $courseList): array
    {
        $levelCourses = [...$courseList];

        $otherCourses = '';

        $failed = '';

        foreach ($semesterResultData->results as $result) {

            [$grade, $score] = self::getGradeScore($result);

            if ($grade === 'F') {
                $failed .= "{$result->courseCode}, ";
            }

            if (array_key_exists($result->courseCode, $levelCourses)) {
                $levelCourses[$result->courseCode] = [
                    'code' => $result->courseCode, 'grade' => $grade, 'score' => $score,
                ];

                continue;
            }

            $otherCourses .= "{$result->courseCode}({$grade}), ";
        }

        return [
            CompositeCourseData::collect(collect($levelCourses)),
            Str::trim($otherCourses, ', '),
            Str::trim($failed, ', '),
        ];
    }

    /** @return array<int, string> */
    private static function getGradeScore(ResultData $result): array
    {
        $grade = 'NR';
        $score = 'NR';

        if ($result->remark !== 'NR') {
            $grade = $result->grade;
            $score = (string) $result->totalScore;
        }

        return [$grade, $score];
    }
}
