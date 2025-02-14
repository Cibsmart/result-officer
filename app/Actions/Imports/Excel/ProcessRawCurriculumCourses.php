<?php

declare(strict_types=1);

namespace App\Actions\Imports\Excel;

use App\Enums\RawDataStatus;
use App\Models\Course;
use App\Models\ExcelImportEvent;
use App\Models\Level;
use App\Models\ProgramCurriculum;
use App\Models\ProgramCurriculumCourse;
use App\Models\ProgramCurriculumElectiveCourse;
use App\Models\ProgramCurriculumElectiveGroup;
use App\Models\ProgramCurriculumLevel;
use App\Models\ProgramCurriculumSemester;
use App\Models\RawCurriculumCourse;
use App\Models\Semester;
use App\Values\CourseCode;
use App\Values\CourseTitle;
use Illuminate\Database\Eloquent\Collection;

final class ProcessRawCurriculumCourses
{
    public static function new(): self
    {
        return new self();
    }

    public function execute(ExcelImportEvent $event): void
    {
        $uniqueCurricula = $this->getDistinctRawCurricula($event);

        foreach ($uniqueCurricula as $curriculum) {
            $curriculumCourses = $this->getPendingRawCurriculumCourses($event, $curriculum);

            $programCurriculum = ProgramCurriculum::getOrCreateFromExcelImport($event, $curriculum);

            $this->processCurriculumCourses($curriculumCourses, $programCurriculum);
        }
    }

    /** @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\RawCurriculumCourse> */
    private function getDistinctRawCurricula(ExcelImportEvent $event): Collection
    {
        return $event->rawCurriculumCourses()
            ->select('curriculum', 'entry_session', 'entry_mode')
            ->distinct()
            ->get();
    }

    /** @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\RawCurriculumCourse> */
    private function getPendingRawCurriculumCourses(
        ExcelImportEvent $event,
        RawCurriculumCourse $curriculum,
    ): Collection {
        return $event->rawCurriculumCourses()
            ->where('status', RawDataStatus::PENDING)
            ->where($curriculum->toArray())
            ->get();
    }

    /** @param \Illuminate\Database\Eloquent\Collection<int, \App\Models\RawCurriculumCourse> $curriculumCourses */
    private function processCurriculumCourses(Collection $curriculumCourses, ProgramCurriculum $programCurriculum): void
    {
        foreach ($curriculumCourses->groupBy('level') as $levelName => $levelCourses) {
            $level = Level::getUsingName((string) $levelName);

            $curriculumLevel = ProgramCurriculumLevel::getOrCreateFromExcelImport($programCurriculum, $level);

            $this->processLevelCourses($levelCourses, $curriculumLevel);
        }
    }

    /** @param \Illuminate\Database\Eloquent\Collection<int, \App\Models\RawCurriculumCourse> $levelCourses */
    private function processLevelCourses(mixed $levelCourses, ProgramCurriculumLevel $curriculumLevel): void
    {
        foreach ($levelCourses->groupBy('semester') as $semesterName => $semesterCourses) {
            $semester = Semester::getUsingName($semesterName);

            $data = [
                'minimum_elective_count' => $semesterCourses->pluck('minimum_elective_count')->unique()->max(),
                'minimum_elective_unit' => $semesterCourses->pluck('minimum_elective_unit')->unique()->max(),
            ];

            $curriculumSemester = ProgramCurriculumSemester::getOrCreateFromExcelImport(
                curriculumLevel: $curriculumLevel,
                semester: $semester,
                data: $data,
            );

            $this->processSemesterCourses($semesterCourses, $curriculumSemester);
        }
    }

    /** @param \Illuminate\Database\Eloquent\Collection<int, \App\Models\RawCurriculumCourse> $semesterCourses */
    private function processSemesterCourses(mixed $semesterCourses, ProgramCurriculumSemester $curriculumSemester): void
    {
        foreach ($semesterCourses as $curriculumCourse) {
            $course = Course::getOrCreateUsingCodeAndTitle(
                CourseCode::new($curriculumCourse->course_code),
                CourseTitle::new($curriculumCourse->course_title),
            );

            if ($course->checkForDuplicateInCurriculumSemester($curriculumSemester)) {
                $curriculumCourse->updateStatus(RawDataStatus::DUPLICATE);

                continue;
            }

            $dbCurriculumCourse = ProgramCurriculumCourse::createFromExcelImport(
                curriculumSemester: $curriculumSemester,
                curriculumCourse: $curriculumCourse,
                course: $course,
            );

            $this->processElectiveGroupings($curriculumCourse, $dbCurriculumCourse);

            $curriculumCourse->updateStatus(RawDataStatus::PROCESSED);
        }
    }

    private function processElectiveGroupings(
        RawCurriculumCourse $curriculumCourse,
        ProgramCurriculumCourse $dbCurriculumCourse,
    ): void {
        if ($curriculumCourse->elective_group === '') {
            return;
        }

        $electiveGroup = ProgramCurriculumElectiveGroup::getOrCreateUsingName(
            curriculumCourse: $dbCurriculumCourse,
            groupName: $curriculumCourse->elective_group,
        );

        ProgramCurriculumElectiveCourse::getOrCreateUsingElectiveGroupAndCourse(
            curriculumCourse: $dbCurriculumCourse, electiveGroup: $electiveGroup,
        );
    }
}
