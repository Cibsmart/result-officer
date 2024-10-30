<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\CourseType;
use App\Enums\CreditUnit;
use App\Enums\EntryMode;
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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class ProgramCurriculumSeeder extends Seeder
{
    public function run(): void
    {
        $content = Storage::get('seeders/program_curriculum_courses.csv');

        assert(! is_null($content));

        $lines = explode("\n", $content);

        $header = [];

        $currentProgram = '';
        $currentCurriculum = '';
        $currentEntrySession = '';
        $currentEntryMode = '';

        $currentLevel = '';
        $currentSemester = '';

        $programCurriculum = null;
        $programCurriculumLevel = null;
        $programCurriculumSemester = null;

        foreach ($lines as $index => $line) {
            /** @var array<int, string> $data */
            $data = str_getcsv($line);

            if ($index === 0) {
                $header = collect($data)->map(fn ($value) => Str::slug($value, '_'))->all();

                continue;
            }

            /** @var array<string, string> $item */
            $item = array_combine($header, $data);

            if ($currentProgram !== $item['program']
                || $currentCurriculum !== $item['curriculum']
                || $currentEntrySession !== $item['entry_session']
                || $currentEntryMode !== $item['entry_mode']) {

                $currentProgram = $item['program'];
                $currentCurriculum = $item['curriculum'];
                $currentEntrySession = $item['entry_session'];
                $currentEntryMode = $item['entry_mode'];

                $programCurriculum = $this->getProgramCurriculum(
                    $currentProgram, $currentCurriculum,
                    $currentEntrySession, $currentEntryMode);
            }

            if ($currentLevel !== $item['level']) {
                $currentLevel = $item['level'];

                $programCurriculumLevel = $this->getProgramCurriculumLevel($currentLevel, $programCurriculum);
            }

            if ($currentSemester !== $item['semester']) {
                $currentSemester = $item['semester'];

                $programCurriculumSemester = $this->getProgramCurriculumSemester($currentSemester,
                    $item['minimum_elective_units'], $programCurriculumLevel);
            }

            $course = Course::getUsingCode($item['course']);
            ProgramCurriculumCourse::query()->create([
                'course_id' => $course->id,
                'course_type' => CourseType::from($item['course_type']),
                'credit_unit' => CreditUnit::from((int) $item['credit_unit']),
                'program_curriculum_semester_id' => $programCurriculumSemester->id,
            ]);

        }
    }

    private function getProgramCurriculum(
        string $currentProgram,
        string $currentCurriculum,
        string $currentEntrySession,
        string $currentEntryMode,
    ): ProgramCurriculum {
        $program = Program::getUsingName($currentProgram);
        $curriculum = Curriculum::getUsingCode($currentCurriculum);
        $entrySession = Session::getUsingName($currentEntrySession);

        return ProgramCurriculum::query()->firstOrCreate([
            'curriculum_id' => $curriculum->id,
            'entry_mode' => EntryMode::get($currentEntryMode),
            'entry_session_id' => $entrySession->id,
            'program_id' => $program->id,
        ]);
    }

    private function getProgramCurriculumLevel(
        string $currentLevel,
        ProgramCurriculum $programCurriculum,
    ): ProgramCurriculumLevel {
        $level = Level::getUsingName($currentLevel);

        return ProgramCurriculumLevel::query()->firstOrCreate([
            'level_id' => $level->id,
            'program_curriculum_id' => $programCurriculum->id,
        ]);
    }

    private function getProgramCurriculumSemester(
        string $currentSemester,
        string $minimumElective,
        ProgramCurriculumLevel $programCurriculumLevel,
    ): ProgramCurriculumSemester {
        $semester = Semester::getUsingName($currentSemester);

        return ProgramCurriculumSemester::query()->firstOrCreate(
            [
                'program_curriculum_level_id' => $programCurriculumLevel->id,
                'semester_id' => $semester->id,
            ],
            ['minimum_elective_units' => CreditUnit::from((int) $minimumElective)],
        );
    }
}
