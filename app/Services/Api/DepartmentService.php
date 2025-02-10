<?php

declare(strict_types=1);

namespace App\Services\Api;

use App\Actions\Imports\Departments\ProcessPortalDepartment;
use App\Actions\Imports\Departments\SavePortalDepartment;
use App\Contracts\PortalService;
use App\Data\Download\PortalDepartmentData;
use App\Enums\ImportEventMethod;
use App\Enums\RawDataStatus;
use App\Http\Clients\DepartmentClient;
use App\Models\ImportEvent;
use Exception;
use Illuminate\Support\Collection;

/** @template-implements \App\Contracts\PortalService<\App\Data\Download\PortalDepartmentData> */
final readonly class DepartmentService implements PortalService
{
    public function __construct(
        private DepartmentClient $client,
        private SavePortalDepartment $saveAction,
        private ProcessPortalDepartment $processAction,
    ) {
    }

    /**
     * @return \Illuminate\Support\Collection<int, \App\Data\Download\PortalDepartmentData>
     * @throws \Exception
     */
    public function getAllDepartments(): Collection
    {
        $departments = $this->client->fetchDepartments();

        return PortalDepartmentData::collect(collect($departments));
    }

    /**
     * @return \Illuminate\Support\Collection<int, \App\Data\Download\PortalDepartmentData>
     * @throws \Exception
     */
    public function getDepartmentDetail(int $onlineId): Collection
    {
        $department = $this->client->fetchDepartmentById($onlineId);

        return PortalDepartmentData::collect(collect($department));
    }

    /**
     * {@inheritDoc}
     * @throws \Exception
     */
    public function get(ImportEventMethod $method, array $parameters): Collection
    {
        if ($method === ImportEventMethod::DEPARTMENT) {
            return $this->getDepartmentDetail((int) $parameters['department_id']);
        }

        return $this->getAllDepartments();
    }

    /** {@inheritDoc} */
    public function save(ImportEvent $event, Collection $data): void
    {
        foreach ($data as $department) {
            $this->saveAction->execute($event, $department);
        }
    }

    public function process(ImportEvent $event): void
    {
        $rawDepartments = $event->departments()->where('status', RawDataStatus::PENDING)->get();

        foreach ($rawDepartments as $rawDepartment) {
            try {
                $this->processAction->execute($rawDepartment);
            } catch (Exception $e) {
                $rawDepartment->setMessage($e->getMessage());

                $rawDepartment->updateStatus(RawDataStatus::FAILED);
            }
        }
    }
}
