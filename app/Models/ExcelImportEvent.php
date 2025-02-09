<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ExcelImportType;
use App\Enums\ImportEventStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

final class ExcelImportEvent extends Model
{
    /** @param array<string, int|string> $data */
    public static function new(
        User $user,
        ExcelImportType $type,
        string $filePath,
        string $fileName,
        array $data = [],
        ImportEventStatus $status = ImportEventStatus::QUEUED,
    ): self {
        $importEvent = new self();

        $importEvent->user_id = $user->id;
        $importEvent->type = $type;
        $importEvent->file_name = $fileName;
        $importEvent->file_path = $filePath;
        $importEvent->status = $status;
        $importEvent->data = $data;

        $importEvent->save();

        return $importEvent;
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\RawFinalResult, \App\Models\ExcelImportEvent> */
    public function rawFinalResults(): HasMany
    {
        return $this->hasMany(RawFinalResult::class);
    }

    public function updateStatus(ImportEventStatus $status): void
    {
        if ($this->status === ImportEventStatus::CANCELLED) {
            return;
        }

        $this->status = $status;
        $this->save();
    }

    public function setMessage(string $message): void
    {
        $this->status = ImportEventStatus::FAILED;
        $this->message = $message;
        $this->save();
    }

    /** @return \Illuminate\Support\Collection<int, string> */
    public function getUniqueRegistrationNumbers(): Collection
    {
        return $this->rawFinalResults()
            ->orderBy('registration_number')
            ->pluck('registration_number')
            ->unique();
    }

    /** @return \Illuminate\Support\Collection<int, \App\Models\RawFinalResult> */
    public function getPendingRawFinalResultsFor(string $registrationNumber): Collection
    {
        return $this->rawFinalResults()
            ->where('status', 'pending')
            ->where('registration_number', $registrationNumber)
            ->orderBy('session')
            ->orderBy('semester')
            ->orderBy('course_code')
            ->get();
    }

    /** @return array{data: 'array', status: 'App\Enums\ImportEventStatus' } */
    protected function casts(): array
    {
        return [
            'data' => 'array',
            'status' => ImportEventStatus::class,
        ];
    }
}
