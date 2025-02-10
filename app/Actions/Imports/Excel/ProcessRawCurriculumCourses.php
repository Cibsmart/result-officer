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
use App\Models\Semester;
use App\Values\CourseCode;

final class ProcessRawCurriculumCourses
{
    public static function new(): self
    {
        return new self();
    }

    public function execute(ExcelImportEvent $event): void
    {
        $uniqueCurricula = $event->rawCurriculumCourses()
            ->select('curriculum', 'entry_session', 'entry_mode')
            ->distinct()
            ->get();

        foreach ($uniqueCurricula as $curriculum) {
            $curriculumCourses = $event->rawCurriculumCourses()
                ->where('status', RawDataStatus::PENDING)
                ->where($curriculum->toArray())
                ->get();

            $programCurriculum = ProgramCurriculum::getOrCreateFromExcelImport($event, $curriculum);

            foreach ($curriculumCourses->groupBy('level') as $levelName => $levelCourses) {
                $level = Level::getUsingName((string) $levelName);

                $curriculumLevel = ProgramCurriculumLevel::getOrCreateFromExcelImport($programCurriculum, $level);

                foreach ($levelCourses->groupBy('semester') as $semesterName => $semesterCourses) {
                    $semester = Semester::getUsingName($semesterName);

                    $minimumElectiveCount = $semesterCourses->pluck('minimum_elective_count')->unique()->max();
                    $minimumElectiveUnit = $semesterCourses->pluck('minimum_elective_unit')->unique()->max();

                    $data = [
                        'minimum_elective_count' => $minimumElectiveCount,
                        'minimum_elective_unit' => $minimumElectiveUnit,
                    ];

                    $curriculumSemester = ProgramCurriculumSemester::getOrCreateFromExcelImport(
                        curriculumLevel: $curriculumLevel,
                        semester: $semester,
                        data: $data,
                    );

                    foreach ($semesterCourses as $curriculumCourse) {
                        $course = Course::getOrCreateUsingCodeAndTitle(
                            CourseCode::new($curriculumCourse->course_code),
                            $curriculumCourse->course_title,
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

                        $curriculumCourse->updateStatus(RawDataStatus::PROCESSED);

                        if ($curriculumCourse->elective_group === '') {
                            continue;
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
            }
        }
    }
}
