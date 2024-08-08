<?php

declare(strict_types=1);

namespace App\Services\Api;

use App\Data\Ingest\PortalDepartmentData;
use App\Http\Clients\DepartmentClient;
use Illuminate\Support\Collection;

final readonly class DepartmentService
{
    public function __construct(private DepartmentClient $client)
    {
    }

    /**
     * @return \Illuminate\Support\Collection<int, \App\Data\Ingest\PortalDepartmentData>
     * @throws \Exception
     */
    public function getAllDepartments(): Collection
    {
        $departments = $this->client->fetchDepartments();

        return PortalDepartmentData::collect(collect($departments));
    }

    /** @throws \Exception */
    public function getDepartmentDetail(string $onlineId): PortalDepartmentData
    {
        $department = $this->client->fetchDepartmentById($onlineId);

        return PortalDepartmentData::from($department);
    }
}
