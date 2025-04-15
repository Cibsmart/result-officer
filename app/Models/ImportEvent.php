<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ImportEventMethod;
use App\Enums\ImportEventStatus;
use App\Enums\ImportEventType;
use Illuminate\Database\Eloquent\Collection;
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
        ImportEventStatus $status = ImportEventStatus::NEW,
    ): self {
        $event = new self();
        $event->user_id = $user->id;
        $event->type = $type;
        $event->method = $method;
        $event->data = $data;
        $event->status = $status;
        $event->save();

        return $event;
    }

    /** @param array<string, int|string> $data */
    public static function inQueue(
        ImportEventType $type,
        ImportEventMethod $method,
        array $data,
    ): bool {
        $events = self::getEventsFor($type, $method);

        foreach ($events as $event) {
            if (count($event->data) === count($data)
                && ! array_diff_assoc($event->data, $data)
                && ! array_diff_assoc($data, $event->data)
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User, $this>
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User, static>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\RawCourse, $this>
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\RawCourse, static>
     */
    public function courses(): HasMany
    {
        return $this->hasMany(RawCourse::class);
    }

    /**
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\RawDepartment, $this>
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\RawDepartment, static>
     */
    public function departments(): HasMany
    {
        return $this->hasMany(RawDepartment::class);
    }

    /**
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\RawStudent, $this>
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\RawStudent, static>
     */
    public function students(): HasMany
    {
        return $this->hasMany(RawStudent::class);
    }

    /**
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\RawRegistration, $this>
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\RawRegistration, static>
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(RawRegistration::class);
    }

    /**
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\RawResult, $this>
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\RawResult, static>
     */
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

            $this->saved = $counts->saved ?? 0;
            $this->processed = $counts->processed ?? 0;
            $this->duplicate = $counts->duplicate ?? 0;
            $this->failed = $counts->failed ?? 0;
            $this->pending = $counts->pending ?? 0;
        }

        $this->status = $status;
        $this->save();
    }

    public function updateDownloadCount(int $count): void
    {
        $this->downloaded = $count;
        $this->save();
    }

    /** @return object{saved?: int, processed?: int, duplicate?: int, failed?: int, pending?: int} */
    public function getCounts(): object
    {
        return $this->{$this->type->value}()->toBase()
            ->selectRaw('count(*) as saved')
            ->selectRaw("count(case when status = 'processed' then 1 end) as processed")
            ->selectRaw("count(case when status = 'duplicate' then 1 end) as duplicate")
            ->selectRaw("count(case when status = 'failed' then 1 end) as failed")
            ->selectRaw("count(case when status = 'pending' then 1 end) as pending")
            ->firstOrFail();
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
        $this->save();
    }

    public function updateMessageAndCounts(
        string $message,
        int $studentCount,
        int $downloadCount,
    ): void {
        $this->message = $message;
        $this->students = $studentCount;
        $this->downloaded = $downloadCount;
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

    /**
     * @phpstan-return \Illuminate\Database\Eloquent\Collection<int, $this>
     * @return \Illuminate\Database\Eloquent\Collection<int, static>
     */
    private static function getEventsFor(
        ImportEventType $type,
        ImportEventMethod $method,
    ): Collection {
        return self::query()
            ->where('type', $type)
            ->where('method', $method)
            ->where('status', ImportEventStatus::QUEUED)
            ->get();
    }
}
