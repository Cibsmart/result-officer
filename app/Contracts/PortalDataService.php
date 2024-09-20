<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\ImportEvent;
use Illuminate\Support\Collection;

/** @template T */
interface PortalDataService
{
    /**
     * @param array<string, int|string> $parameters
     * @return \Illuminate\Support\Collection<int, T>
     */
    public function get(array $parameters): Collection;

    /** @param \Illuminate\Support\Collection<int, T> $data */
    public function save(ImportEvent $event, Collection $data): void;

    public function process(ImportEvent $event): void;
}
