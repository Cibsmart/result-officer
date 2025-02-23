<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Data\Session\SessionListData;

final class SessionController
{
    public function __invoke(): SessionListData
    {
        return SessionListData::new();
    }
}
