<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Data\States\StateListData;

final class StateController
{
    public function __invoke(): StateListData
    {
        return StateListData::new();
    }
}
