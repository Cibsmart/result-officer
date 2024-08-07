<?php

declare(strict_types=1);

namespace App\Http\Clients;

final class DepartmentClient extends ApiClient
{
    /**
     * @return array<int, array{id: string, code: string, name: string, faculty: string, options: array{id: string,
     *     name: string}}>
     * @throws \Exception
     */
    public function fetchDepartments(): array
    {
        /**
         * phpcs:ignore SlevomatCodingStandard.Files.LineLength
         * @var array<int, array{id: string, code: string, name: string, faculty: string, options: array{id: string, name: string}}> $departments
         */
        $departments = $this->get('departments');

        return $departments;
    }

    /**
     * @return array<int, array{id: string, name: string}>
     * @throws \Exception
     */
    public function fetchDepartmentById(string $id): array
    {
        /** @var array<int, array{id: string, name: string}> $department */
        $department = $this->get("departments/$id");

        return $department;
    }
}
