<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Data\Response\ResponseData;
use App\Enums\NotificationType;
use Illuminate\Support\Collection;

final readonly class GetResponse
{
    public function __construct(public NotificationType $type, public string $message)
    {
    }

    /** @param \Illuminate\Support\Collection<int, \App\Data\Response\ResponseData> $data */
    public static function fromArray(Collection $data): self
    {
        if ($data->count() === 0) {
            return new self(NotificationType::INFO, 'No record returned from API');
        }

        $failed = $data->filter(fn ($record) => $record->message !== true);

        $numberSuccessful = $data->count() - $failed->count();

        $successMessage = "{$numberSuccessful} records downloaded and saved successfully out of {$data->count()} records";

        if ($failed->count() === 0) {
            return new self(NotificationType::SUCCESS, $successMessage);
        }

        $groupFailed = self::groupFailedResponseByMessage($failed);

        $failureMessage = "{$failed->count()} failed to save with error messages: $groupFailed";

        if ($failed->count() === $data->count()) {
            return new self(NotificationType::ERROR, $failureMessage);
        }

        return new self(NotificationType::INFO, "SUCCESS: $successMessage. FAIL: $failureMessage");
    }

    /** @param \Illuminate\Support\Collection<int, \App\Data\Response\ResponseData> $failed */
    private static function groupFailedResponseByMessage(Collection $failed): string
    {
        $grouped = $failed->mapToGroups(fn (ResponseData $data) => [$data->message => $data->key]);

        return "MESSAGES: {$grouped->keys()->toJson()}, AFFECTED: {$grouped->values()->toJson()}";
    }
}
