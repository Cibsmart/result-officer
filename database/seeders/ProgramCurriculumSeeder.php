<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\CourseType;
use App\Enums\CreditUnit;
use App\Enums\EntryMode;
use App\Helpers\CSVFile;
use App\Models\Course;
use App\Models\Curriculum;
use App\Models\Level;
use App\Models\Program;
use App\Models\ProgramCurriculum;
use App\Models\ProgramCurriculumCourse;
use App\Models\ProgramCurriculumLevel;
use App\Models\ProgramCurriculumSemester;
use App\Models\Semester;
use App\Models\Session;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

final class ProgramCurriculumSeeder extends Seeder
{
    public function run(): void
    {
        /** @var \Illuminate\Support\Collection<int, \Illuminate\Support\Collection<string, string>> $content */
        $content = (new CSVFile('seeders/program_courses.csv'))->read();

        $programCurriculumCourses = $content->sortBy([
            ['program', 'asc'],
            ['curriculum', 'asc'],
            ['entry_session', 'asc'],
            ['entry_mode', 'desc'],
            ['level', 'asc'],
            ['semester', 'asc'],
            ['course_code', 'asc'],
        ])->groupBy('program');

        foreach ($programCurriculumCourses as $programCode => $programCourses) {
            foreach ($programCourses->groupBy('curriculum') as $curriculumName => $curriculumCourses) {
                $this->createProgramCurriculum($programCode, $curriculumName, $curriculumCourses);
            }
        }

        //        $this->call([CourseAlternativeSeeder::class]);
    }

    /** @param \Illuminate\Support\Collection<int, \Illuminate\Support\Collection<string, string>> $curriculumCourses */
    private function createProgramCurriculum(
        string $programCode,
        string $curriculumName,
        Collection $curriculumCourses,
    ): void {
        foreach ($curriculumCourses->groupBy('entry_session') as $entrySessionName => $sessionCourses) {
            foreach ($sessionCourses->groupBy('entry_mode') as $entryMode => $entryModeCourses) {
                $program = Program::getUsingCode($programCode);
                $curriculum = Curriculum::getUsingCode($curriculumName);
                $entrySession = Session::getUsingName($entrySessionName);

                $programCurriculum = ProgramCurriculum::query()->firstOrCreate([
                    'curriculum_id' => $curriculum->id,
                    'entry_mode' => EntryMode::get($entryMode),
                    'entry_session_id' => $entrySession->id,
                    'program_id' => $program->id,
                ]);

                $this->createProgramCurriculumLevel($programCurriculum, $entryModeCourses);
            }
        }
    }

    /** @param \Illuminate\Support\Collection<int, \Illuminate\Support\Collection<string, string>> $entryModeCourses */
    private function createProgramCurriculumLevel(
        ProgramCurriculum $programCurriculum,
        Collection $entryModeCourses,
    ): void {
        foreach ($entryModeCourses->groupBy('level') as $levelName => $levelCourses) {
            $level = Level::getUsingName((string) $levelName);

            $programCurriculumLevel = ProgramCurriculumLevel::query()->firstOrCreate([
                'level_id' => $level->id,
                'program_curriculum_id' => $programCurriculum->id,
            ]);

            $this->createProgramCurriculumSemester($programCurriculumLevel, $levelCourses);
        }
    }

    /** @param \Illuminate\Support\Collection<int, \Illuminate\Support\Collection<string, string>> $levelCourses */
    private function createProgramCurriculumSemester(
        ProgramCurriculumLevel $programCurriculumLevel,
        Collection $levelCourses,
    ): void {
        foreach ($levelCourses->groupBy('semester') as $semesterName => $semesterCourses) {
            $semester = Semester::getUsingName($semesterName);

            $programCurriculumSemester = ProgramCurriculumSemester::query()->firstOrCreate(
                [
                    'program_curriculum_level_id' => $programCurriculumLevel->id,
                    'semester_id' => $semester->id,
                ],
                [
                    'minimum_elective_count' => CreditUnit::from(
                        (int) $semesterCourses->first()['minimum_elective_count'],
                    ),
                    'minimum_elective_units' => CreditUnit::from(
                        (int) $semesterCourses->first()['minimum_elective_units'],
                    ),
                ],
            );

            $this->createProgramCurriculumCourses($programCurriculumSemester, $semesterCourses);
        }
    }

    /** @param \Illuminate\Support\Collection<int, \Illuminate\Support\Collection<string, string>> $semesterCourses */
    private function createProgramCurriculumCourses(
        ProgramCurriculumSemester $programCurriculumSemester,
        Collection $semesterCourses,
    ): void {

        $electiveGroups = [];

        foreach ($semesterCourses as $semesterCourse) {
            $course = Course::getUsingCode($semesterCourse['course_code']);

            $courseType = CourseType::from($semesterCourse['course_type']);

            $programCurriculumCourse = ProgramCurriculumCourse::query()->create([
                'course_id' => $course->id,
                'course_type' => $courseType,
                'credit_unit' => CreditUnit::from((int) $semesterCourse['credit_unit']),
                'program_curriculum_semester_id' => $programCurriculumSemester->id,
            ]);

            $electiveGrouping = $semesterCourse['elective_group'];
            assert(! is_null($electiveGrouping));

            if (! $this->isElectiveWithGrouping($courseType, $electiveGrouping)) {
                continue;
            }

            $electiveGroups[$electiveGrouping][] = $programCurriculumCourse;
        }

        $this->createProgramCurriculumElectiveGroups($programCurriculumSemester, $electiveGroups);
    }

    private function isElectiveWithGrouping(CourseType $courseType, string $electiveGrouping): bool
    {
        return $courseType === CourseType::ELECTIVE && $electiveGrouping !== '';
    }

    /** @param array<string, \App\Models\ProgramCurriculumCourse> $electiveGroups */
    private function createProgramCurriculumElectiveGroups(
        ProgramCurriculumSemester $programCurriculumSemester,
        array $electiveGroups,
    ): void {
        foreach ($electiveGroups as $electiveGrouping => $programCurriculumCourses) {
            if (count($programCurriculumCourses) <= 1) {
                continue;
            }

            $curriculumElectiveGroup = $programCurriculumSemester
                ->programCurriculumElectiveGroups()
                ->create(['name' => $electiveGrouping]);

            foreach ($programCurriculumCourses as $programCurriculumCourse) {
                $curriculumElectiveGroup->programCurriculumElectiveCourses()
                    ->create(['program_curriculum_course_id' => $programCurriculumCourse->id]);
            }
        }
    }
}
