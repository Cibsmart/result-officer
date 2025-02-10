<?php

declare(strict_types=1);

namespace App\Services\Api;

use App\Actions\Imports\Courses\ProcessPortalCourse;
use App\Actions\Imports\Courses\SavePortalCourse;
use App\Contracts\PortalService;
use App\Data\Download\PortalCourseData;
use App\Enums\ImportEventMethod;
use App\Enums\RawDataStatus;
use App\Http\Clients\CourseClient;
use App\Models\ImportEvent;
use Exception;
use Illuminate\Support\Collection;

/** @template-implements \App\Contracts\PortalService<\App\Data\Download\PortalCourseData> */
final readonly class CourseService implements PortalService
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

    /**
     * {@inheritDoc}
     * @throws \Exception
     */
    public function get(ImportEventMethod $method, array $parameters): Collection
    {
        if ($method === ImportEventMethod::COURSE) {
            return $this->getCourseDetail((int) $parameters['course_id']);
        }

        return $this->getAllCourses();
    }

    /** {@inheritDoc} */
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
}
