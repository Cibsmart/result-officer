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
use App\Models\Student;
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

        $programStudents = $this->getProgramStudents($program, $sessionId, $levelId, $semesterId);

        $students = $this->prepareCompositeRows($programStudents, $courses, $sessionId, $semesterId);

        return Inertia::render('reports/composite/view/page', new CompositeViewPage(
            students: $students, courses: CompositeCourseListData::collect($courses),
        ));
    }

    /**
     * @param \Illuminate\Support\LazyCollection<int, \App\Models\Student> $students
     * @param \Illuminate\Support\Collection<int, array{code: string, unit: int}> $courses
     * @return \Illuminate\Support\Collection<int, \App\Data\Composite\CompositeRowData>
     */
    public function prepareCompositeRows(
        LazyCollection $students,
        Collection $courses,
        int $sessionId,
        int $semesterId,
    ): Collection {
        $crossTab = [];

        foreach ($students as $student) {
            $courseList = $courses->pluck('unit', 'code')
                ->map(fn () => ['code' => '', 'grade' => '-', 'score' => '-'])->all();

            $semesterResultData = $this->getSemesterResultDataForStudent($student, $sessionId, $semesterId);

            [$levelCourses, $otherCourses] = $this->prepareCompositeCourses($semesterResultData, $courseList);

            $crossTab[] = [
                'creditUnitTotal' => $semesterResultData->creditUnitTotal,
                'gradePointAverage' => $semesterResultData->formattedGPA,
                'gradePointTotal' => $semesterResultData->gradePointTotal,
                'id' => $student->id,
                'levelCourses' => $levelCourses,
                'name' => $student->name,
                'otherCourses' => $otherCourses,
                'registrationNumber' => $student->registration_number,
            ];
        }

        return CompositeRowData::collect(collect($crossTab));
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

    /** @return \Illuminate\Support\LazyCollection<int, \App\Models\Student> */
    private function getProgramStudents(
        Program $program,
        int $sessionId,
        int $levelId,
        int $semesterId,
    ): LazyCollection {
        return $program->students()
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
    }

    private function getSemesterResultDataForStudent(
        Student $student,
        int $sessionId,
        int $semesterId,
    ): SemesterResultData {
        $sessionEnrollment = $student->enrollments
            ->where('session_id', '===', $sessionId)
            ->firstOrFail();

        $semesterEnrollment = $sessionEnrollment
            ->semesters
            ->where('semester_id', '===', $semesterId)
            ->firstOrFail();

        return SemesterResultData::fromModel($semesterEnrollment);
    }

    /**
     * @param array<string, array<string, string>> $courseList
     * @return array<int, \Illuminate\Support\Collection<int, \App\Data\Composite\CompositeCourseData>>
     */
    private function prepareCompositeCourses(SemesterResultData $semesterResultData, array $courseList): array
    {
        $levelCourses = [...$courseList];

        $otherCourses = [];

        foreach ($semesterResultData->results as $result) {

            $grade = 'NR';
            $score = 'NR';

            if ($result->remark !== 'NR') {
                $grade = $result->grade;
                $score = (string) $result->totalScore;
            }

            if (array_key_exists($result->courseCode, $levelCourses)) {
                $levelCourses[$result->courseCode]['code'] = $result->courseCode;
                $levelCourses[$result->courseCode]['grade'] = $grade;
                $levelCourses[$result->courseCode]['score'] = $score;

                continue;
            }

            $otherCourses[] = ['code' => $result->courseCode, 'grade' => $grade, 'score' => $score];
        }

        return [
            CompositeCourseData::collect(collect($levelCourses)),
            CompositeCourseData::collect(collect($otherCourses)),
        ];
    }
}
