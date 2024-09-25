<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ImportEventMethod;
use App\Enums\ImportEventStatus;
use App\Enums\ImportEventType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class ImportEvent extends Model
{
    /** @param array<string, int|string> $data */
    public static function new(
        User $user,
        ImportEventType $type,
        ImportEventMethod $method,
        array $data,
    ): self {
        $event = new self();
        $event->user_id = $user->id;
        $event->type = $type;
        $event->method = $method;
        $event->data = $data;
        $event->status = ImportEventStatus::NEW;
        $event->save();

        return $event;
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User, \App\Models\ImportEvent> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\RawCourse> */
    public function courses(): HasMany
    {
        return $this->hasMany(RawCourse::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\RawDepartment> */
    public function departments(): HasMany
    {
        return $this->hasMany(RawDepartment::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\RawStudent> */
    public function students(): HasMany
    {
        return $this->hasMany(RawStudent::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\RawRegistration> */
    public function registrations(): HasMany
    {
        return $this->hasMany(RawRegistration::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\RawResult> */
    public function results(): HasMany
    {
        return $this->hasMany(RawResult::class);
    }

    public function updateStatus(ImportEventStatus $status): void
    {
        if ($this->status === ImportEventStatus::CANCELLED) {
            return;
        }

        if ($status === ImportEventStatus::COMPLETED) {
            $counts = $this->getCounts();

            $this->saved_count = $counts->saved_count;
            $this->processed_count = $counts->processed_count;
            $this->failed_count = $counts->failed_count;
            $this->unprocessed_count = $counts->unprocessed_count;
        }

        $this->status = $status;
        $this->save();
    }

    public function updateDownloadCount(int $count): void
    {
        $this->download_count = $count;
        $this->save();
    }

    /** @return object{saved_count: int, processed_count: int, failed_count: int, unprocessed_count: int} */
    public function getCounts(): object
    {
        return $this->{$this->type->value}()->toBase()
            ->selectRaw('count(*) as saved_count')
            ->selectRaw("count(case when status = 'processed' then 1 end) as processed_count")
            ->selectRaw("count(case when status = 'failed' then 1 end) as failed_count")
            ->selectRaw("count(case when status = 'pending' then 1 end) as unprocessed_count")
            ->firstOrFail();
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
        $this->save();
    }

    /**
     * @return array{data: 'json', status: 'App\Enums\ImportEventStatus',
     *     type: 'App\Enums\ImportEventType', method: 'App\Enums\ImportEventMethod', }
     */
    protected function casts(): array
    {
        return [
            'data' => 'json',
            'method' => ImportEventMethod::class,
            'status' => ImportEventStatus::class,
            'type' => ImportEventType::class,
        ];
    }
}
