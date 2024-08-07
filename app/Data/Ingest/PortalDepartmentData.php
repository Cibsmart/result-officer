<?php

declare(strict_types=1);

namespace App\Data\Ingest;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

final class PortalDepartmentData extends Data
{
    public function __construct(
        public string $onlineId,
        public string $departmentCode,
        public string $departmentName,
        public string $facultyName,
        /** @var \Illuminate\Support\Collection<int, \App\Data\Ingest\PortalProgramData> */
        public Collection $programs,
    ) {
    }

    /**
     * @param array{id: string, code: string, name: string, faculty: string, options: array<int, array<string,
     *     string>>} $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            onlineId: $data['id'],
            departmentCode: $data['code'],
            departmentName: $data['name'],
            facultyName: $data['faculty'],
            programs: PortalProgramData::collect(collect($data['options'])),
        );
    }
}
