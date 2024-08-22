<?php

declare(strict_types=1);

namespace App\Http\Clients;

use Config;

/**
 * phpcs:ignore SlevomatCodingStandard.Files.LineLength
 * @phpstan-type DepartmentDetail array{id:string, code:string, name:string, faculty:string, options:array{id:string,
 *     name:string}}
 */
final readonly class DepartmentClient extends ApiClient
{
    private string $endpoint;

    public function __construct()
    {
        $this->endpoint = Config::string('rp_http.endpoints.departments');
    }

    /**
     * @return array<DepartmentDetail>
     * @throws \Exception
     */
    public function fetchDepartments(): array
    {
        /** @var array<DepartmentDetail> $departments */
        $departments = $this->get($this->endpoint);

        return $departments;
    }

    /**
     * @return array<DepartmentDetail>
     * @throws \Exception
     */
    public function fetchDepartmentById(string $id): array
    {
        /** @var array<DepartmentDetail> $department */
        $department = $this->get(
            endpoint: $this->endpoint,
            parameters: ['department_id' => $id],
        );

        return $department;
    }
}
