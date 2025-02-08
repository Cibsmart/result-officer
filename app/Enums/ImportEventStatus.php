<?php

declare(strict_types=1);

namespace App\Enums;

enum ImportEventStatus: string
{
    case NEW = 'new';
    case QUEUED = 'queued';
    case STARTED = 'started';
    case DOWNLOADING = 'downloading';
    case DOWNLOADED = 'downloaded';
    case UPLOADING = 'uploading';
    case UPLOADED = 'uploaded';
    case SAVING = 'saving';
    case SAVED = 'saved';
    case PROCESSING = 'processing';
    case CANCELLED = 'cancelled';
    case FAILED = 'failed';
    case COMPLETED = 'completed';

    /** @return array<\App\Enums\ImportEventStatus> */
    public static function showOnProgressBar(): array
    {
        return [self::STARTED, self::DOWNLOADING, self::SAVING, self::PROCESSING, self::COMPLETED];
    }

    /** @return array<\App\Enums\ImportEventStatus> */
    public static function unprocessableStates(): array
    {
        return [self::CANCELLED, self::FAILED, self::COMPLETED];
    }

    public function width(): int
    {
        return match ($this) {
            self::NEW, self::STARTED, self::QUEUED => 1,
            self::DOWNLOADING, self::DOWNLOADED, self::UPLOADING, self::UPLOADED => 25,
            self::SAVING, self::SAVED => 50,
            self::PROCESSING => 75,
            self::CANCELLED, self::FAILED, self::COMPLETED => 100,
        };
    }

    public function color(): StatusColor
    {
        return match ($this) {
            self::NEW, self::STARTED, self::QUEUED, self::CANCELLED => StatusColor::GRAY,
            self::DOWNLOADING, self::DOWNLOADED, self::UPLOADING, self::UPLOADED => StatusColor::INDIGO,
            self::SAVING, self::SAVED => StatusColor::PINK,
            self::PROCESSING => StatusColor::PURPLE,
            self::FAILED => StatusColor::RED,
            self::COMPLETED => StatusColor::GREEN,
        };
    }
}
