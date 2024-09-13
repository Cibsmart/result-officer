<?php

declare(strict_types=1);

namespace App\Services\Api;

use App\Data\Download\PortalDepartmentData;
use App\Http\Clients\DepartmentClient;
use Illuminate\Support\Collection;

final readonly class DepartmentService
{
    public function __construct(private DepartmentClient $client)
    {
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
}
