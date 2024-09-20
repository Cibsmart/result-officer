<?php

declare(strict_types=1);

namespace App\Services\Api;

use App\Actions\Courses\ProcessPortalCourse;
use App\Actions\Courses\SavePortalCourse;
use App\Contracts\PortalDataService;
use App\Data\Download\PortalCourseData;
use App\Enums\RawDataStatus;
use App\Http\Clients\CourseClient;
use App\Models\ImportEvent;
use Exception;
use Illuminate\Support\Collection;

/** @template-implements \App\Contracts\PortalDataService<\App\Data\Download\PortalCourseData> */
final readonly class CourseService implements PortalDataService
{
    public function __construct(
        private CourseClient $client,
        private SavePortalCourse $saveAction,
        private ProcessPortalCourse $processAction,
    ) {
    }

    /**
     * @return \Illuminate\Support\Collection<int, \App\Data\Download\PortalCourseData>
     * @throws \Exception
     */
    public function getAllCourses(): Collection
    {
        $courses = $this->client->fetchCourses();

        return PortalCourseData::collect(collect($courses));
    }

    /**
     * @return \Illuminate\Support\Collection<int, \App\Data\Download\PortalCourseData>
     * @throws \Exception
     */
    public function getCourseDetail(int $onlineId): Collection
    {
        $course = $this->client->fetchCourseById($onlineId);

        return PortalCourseData::collect(collect($course));
    }

    /** @param \Illuminate\Support\Collection<int, \App\Data\Download\PortalCourseData> $data */
    public function save(ImportEvent $event, Collection $data): void
    {
        foreach ($data as $course) {
            $this->saveAction->execute($event, $course);
        }
    }

    public function process(ImportEvent $event): void
    {
        $rawCourses = $event->courses()->where('status', RawDataStatus::PENDING)->get();

        foreach ($rawCourses as $rawCourse) {
            try {
                $this->processAction->execute($rawCourse);
            } catch (Exception $e) {
                $rawCourse->setMessage($e->getMessage());

                $rawCourse->updateStatus(RawDataStatus::FAILED);
            }
        }
    }

    /**
     * {@inheritDoc}
     * @throws \Exception
     */
    public function get(array $parameters): Collection
    {
        if (count($parameters) === 0) {
            return $this->getAllCourses();
        }

        return $this->getCourseDetail((int) $parameters['course_id']);
    }
}
