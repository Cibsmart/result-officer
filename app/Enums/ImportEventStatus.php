<?php

declare(strict_types=1);

namespace App\Enums;

enum ImportEventStatus: string
{
    case NEW = 'new';
    case STARTED = 'started';
    case DOWNLOADING = 'downloading';
    case SAVING = 'saving';
    case SAVED = 'saved';
    case PROCESSING = 'processing';
    case FAILED = 'failed';
    case COMPLETED = 'completed';

    /** @return array<\App\Enums\ImportEventStatus> */
    public static function showOnProgressBar(): array
    {
        return [self::STARTED, self::DOWNLOADING, self::SAVING, self::PROCESSING, self::COMPLETED];
    }

    public function width(): int
    {
        return match ($this) {
            self::NEW, self::STARTED => 1,
            self::DOWNLOADING => 25,
            self::SAVING, self::SAVED => 50,
            self::PROCESSING => 75,
            self::FAILED, self::COMPLETED => 100,
        };
    }
}
