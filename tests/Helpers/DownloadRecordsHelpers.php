<?php

declare(strict_types=1);

/**
 * @param array<int, array<string, string>> $data
 * @param array<string, string> $groups
 * @return array<int, array<string, string>>
 */
function groupArrays(array $data, array $groups): array
{
    return groupArrayBy($data, $groups);
}

/**
 * @param array<int, array<string, string>> $data
 * @param array<string, string> $groups
 * @return array<string, string>
 */
function groupArrayBy(array $data, array $groups, int $index = 0): array
{
    if ($index === count($groups)) {
        return $data;
    }

    $keys = array_keys($groups);
    $values = array_values($groups);

    $grouped = collect($data)->groupBy($keys[$index])[$values[$index]]->all();

    return groupArrayBy($grouped, $groups, $index + 1);
}
