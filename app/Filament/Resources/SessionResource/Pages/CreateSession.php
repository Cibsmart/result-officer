<?php

declare(strict_types=1);

namespace App\Filament\Resources\SessionResource\Pages;

use App\Filament\Resources\SessionResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateSession extends CreateRecord
{
    protected static string $resource = SessionResource::class;

}
