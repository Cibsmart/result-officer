<?php

declare(strict_types=1);

namespace App\Services\Api;

use App\Contracts\PortalService;
use App\Enums\ImportEventType;

final class PortalServiceFactory
{
    public function resolve(ImportEventType $type): PortalService
    {
        return resolve($type->service());
    }
}
