<?php

declare(strict_types=1);

/**
 * Guards against a migration whose down() drops a table its up() never created.
 *
 * Three migrations shipped with this defect, including a mutually-swapped pair:
 * create_registrations_table dropped `results` and create_results_table dropped
 * `registrations`, so rolling back over that range destroyed the two most
 * valuable tables in the system while leaving both intended targets in place.
 *
 * Only literal table names are compared — migrations that resolve their table
 * from config (activitylog, pulse) are covered by whichever names they do state
 * literally.
 */
it('drops exactly the tables it creates in every migration', function (string $migration): void {
    $source = (string) file_get_contents($migration);

    preg_match_all("/Schema::create\(\s*'([^']+)'/", $source, $creates);
    preg_match_all("/dropIfExists\(\s*'([^']+)'/", $source, $drops);

    $created = array_unique($creates[1]);
    $dropped = array_unique($drops[1]);

    sort($created);
    sort($dropped);

    expect($dropped)->toBe($created);
})->with(function (): array {
    $migrations = glob(__DIR__ . '/../../database/migrations/*.php');

    if ($migrations === false) {
        return [];
    }

    return array_values(array_filter(
        $migrations,
        static fn (string $path): bool => str_contains((string) file_get_contents($path), 'Schema::create('),
    ));
});
