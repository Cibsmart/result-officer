<?php

declare(strict_types=1);

namespace App\Data\Scores;

use App\Values\ExamScore;
use App\Values\InCourseScore;
use App\Values\TotalScore;

final readonly class ScoresData
{
    public function __construct(
        public TotalScore $total,
        public ExamScore $exam,
        public InCourseScore $inCourse,
        public InCourseScore $inCourse2,
    ) {
    }

    public static function new(
        int $exam,
        int $inCourse,
        int $inCourseScore2,
    ): self {
        $inCourseScore = InCourseScore::new($inCourse);
        $inCourseScore2 = InCourseScore::new($inCourseScore2);
        $examScore = ExamScore::new($exam);

        return new self(
            total: TotalScore::new($examScore->value + $inCourseScore->value + $inCourseScore2->value),
            exam: $examScore,
            inCourse: $inCourseScore,
            inCourse2: $inCourseScore2,
        );
    }

    /** @param array{exam?: int, in_course?: int, in_course_2?: int} $scores */
    public static function fromArray(array $scores): self
    {
        $exam = array_key_exists('exam', $scores)
            ? $scores['exam']
            : 0;

        $inCourse = array_key_exists('in_course', $scores)
            ? $scores['in_course']
            : 0;

        $inCourse2 = array_key_exists('in_course_2', $scores)
            ? $scores['in_course_2']
            : 0;

        return self::new($exam, $inCourse, $inCourse2);
    }

    /**
     * @param array{exam: int, in_course: int, in_course_2: int} $oldScores
     * @param array{exam?: int, in_course?: int, in_course_2?: int} $newScores
     */
    public static function update(array $oldScores, array $newScores): self
    {
        $exam = array_key_exists('exam', $newScores)
            ? $newScores['exam']
            : $oldScores['exam'];

        $inCourse = array_key_exists('in_course', $newScores)
            ? $newScores['in_course']
            : $oldScores['in_course'];

        $inCourse2 = array_key_exists('in_course_2', $newScores)
            ? $newScores['in_course_2']
            : $oldScores['in_course_2'];

        return self::new($exam, $inCourse, $inCourse2);
    }

    /** @return array{exam: int, in_course: int, in_course_2: int} */
    public function value(): array
    {
        return [
            'exam' => $this->exam->value,
            'in_course' => $this->inCourse->value,
            'in_course_2' => $this->inCourse2->value,
        ];
    }
}
