<?php

declare(strict_types=1);

namespace App\Http\Controllers\Reports;

use App\Data\Composite\CompositeCourseData;
use App\Data\Composite\CompositeCourseListData;
use App\Data\Composite\CompositeRowData;
use App\Data\Level\LevelListData;
use App\Data\Program\ProgramListData;
use App\Data\Results\SemesterResultData;
use App\Data\Semester\SemesterListData;
use App\Data\Session\SessionListData;
use App\Models\Program;
use App\Models\ProgramCourse;
use App\Models\ProgramCurriculum;
use App\ViewModels\Reports\CompositeFormPage;
use App\ViewModels\Reports\CompositeViewPage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Inertia\Inertia;
use Inertia\Response;

final readonly class CompositeSheetController
{
    public function form(): Response
    {
        return Inertia::render('reports/composite/form/page', new CompositeFormPage(
            program: ProgramListData::new(),
            semester: SemesterListData::new(),
            session: SessionListData::new(),
            level: LevelListData::new(),
        ));
    }

    public function view(Request $request): Response
    {
        $program = Program::query()->where('id', $request->input('department.id'))->firstOrFail();

        $sessionId = $request->input('session.id');
        $levelId = $request->input('level.id');
        $semesterId = $request->input('semester.id');

        $courses = $this->getCourses($program->id, $sessionId, $levelId, $semesterId);

        $students = $this->getStudentAndResults($program, $sessionId, $levelId, $semesterId, $courses);

        return Inertia::render('reports/composite/view/page', new CompositeViewPage(
            students: $students, courses: CompositeCourseListData::collect($courses),
        ));
    }

    /**
     * @param \Illuminate\Support\Collection<int, array{code: string, unit: int}> $courses
     * @return \Illuminate\Support\Collection<int, \App\Data\Composite\CompositeRowData>
     */
    public function getStudentAndResults(
        Program $program,
        int $sessionId,
        int $levelId,
        int $semesterId,
        Collection $courses,
    ): Collection {
        $students = $program->students()
            ->with([
                'enrollments.semesters.courses.course',
                'enrollments.semesters.courses.result',
                'enrollments.semesters.semester',
            ])
            ->whereHas('enrollments.semesters',
                function (Builder $query) use ($sessionId, $levelId, $semesterId): void {
                    $query->where('session_id', $sessionId)
                        ->where('level_id', $levelId)
                        ->where('semester_id', $semesterId);
                })
            ->orderBy('registration_number')
            ->lazy();

        return $this->prepareCrossTab($students, $sessionId, $semesterId, $courses);
    }

    /** @return \Illuminate\Support\Collection<int, array{code: string, unit: int}> */
    public function getCourses(
        int $programId,
        int $sessionId,
        int $levelId,
        int $semesterId,
    ): Collection {
        $curriculum = ProgramCurriculum::query()
            ->where('program_id', $programId)
            ->where('session_id', $sessionId)
            ->where('level_id', $levelId)
            ->where('semester_id', $semesterId)
            ->first();

        return $curriculum
            ->courses()
            ->select('program_courses.*')
            ->join('courses', 'courses.id', '=', 'program_courses.course_id')
            ->orderBy('courses.code')
            ->with('course')
            ->get()
            ->map(
                fn (ProgramCourse $course) => ['code' => $course->course->code, 'unit' => $course->credit_unit],
            );
    }

    /**
     * @param \Illuminate\Support\LazyCollection<int, \App\Models\Student> $students
     * @param \Illuminate\Support\Collection<int, array{code: string, unit: int}> $courses
     * @return \Illuminate\Support\Collection<int, \App\Data\Composite\CompositeRowData>
     */
    private function prepareCrossTab(
        LazyCollection $students,
        int $sessionId,
        int $semesterId,
        Collection $courses,
    ): Collection {
        $crossTab = [];

        foreach ($students as $student) {
            $row = [
                'id' => $student->id, 'name' => $student->name, 'registrationNumber' => $student->registration_number,
            ];

            $courseList = $courses->pluck('unit', 'code')->map(fn () => [
                'code' => '', 'grade' => '-', 'score' => '-',
            ])->all();

            $sessionEnrollment = $student->enrollments
                ->where('session_id', '===', $sessionId)
                ->firstOrFail();

            $semesterEnrollment = $sessionEnrollment
                ->semesters
                ->where('semester_id', '===', $semesterId)
                ->firstOrFail();

            $otherCourses = [];

            $semesterEnrollmentData = SemesterResultData::fromModel($semesterEnrollment);

            $row['creditUnitTotal'] = $semesterEnrollmentData->creditUnitTotal;
            $row['gradePointTotal'] = $semesterEnrollmentData->gradePointTotal;
            $row['gradePointAverage'] = $semesterEnrollmentData->formattedGPA;

            foreach ($semesterEnrollmentData->results as $result) {

                $grade = $result->remark === 'NR'
                    ? $result->remark
                    : $result->grade;
                $score = $result->remark === 'NR'
                    ? $result->remark
                    : (string) $result->totalScore;

                if (array_key_exists($result->courseCode, $courseList)) {
                    $courseList[$result->courseCode]['code'] = $result->courseCode;
                    $courseList[$result->courseCode]['grade'] = $grade;
                    $courseList[$result->courseCode]['score'] = $score;

                    continue;
                }

                $otherCourses[] = ['code' => $result->courseCode, 'grade' => $grade, 'score' => $score];
            }

            $row['levelCourses'] = CompositeCourseData::collect(collect($courseList));
            $row['otherCourses'] = CompositeCourseData::collect(collect($otherCourses));

            $crossTab[] = $row;
        }

        return CompositeRowData::collect(collect($crossTab));
    }
}
