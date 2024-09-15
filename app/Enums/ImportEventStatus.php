<?php

declare(strict_types=1);

namespace App\Enums;

enum ImportEventStatus: string
{
    case NEW = 'new';
    case STARTED = 'started';
    case DOWNLOADING = 'downloading';
    case DOWNLOADED = 'downloaded';
    case SAVING = 'saving';
    case PROCESSING = 'processing';
    case FAILED = 'failed';
    case COMPLETED = 'completed';

    public function width(): int
    {
        return match ($this) {
            self::NEW, self::STARTED => 1,
            self::DOWNLOADING, self::DOWNLOADED => 25,
            self::SAVING => 50,
            self::PROCESSING => 75,
            self::FAILED, self::COMPLETED => 100,
        };
    }
}
