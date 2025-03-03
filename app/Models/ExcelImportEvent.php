<?php

declare(strict_types=1);

namespace App\Models;

use App\Actions\Imports\Excel\ValidateHeadings;
use App\Enums\ExcelImportType;
use App\Enums\ImportEventStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\HeadingRowImport;

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

    public static function inQueue(
        ExcelImportType $type,
        string $fileName,
    ): bool {
        $events = self::query()
            ->where('type', $type)
            ->where('file_name', $fileName)
            ->whereNot('status', ImportEventStatus::FAILED)
            ->get();

        return $events->isNotEmpty();
    }

    /** @return array{passed: bool, message: string} */
    public static function validateExcelFileHeadingsAndCheckQueue(
        UploadedFile $file,
        ExcelImportType $type,
        string $fileName,
    ): array {
        $result = ['passed' => true, 'message' => ''];

        $headings = (new HeadingRowImport())->toArray($file)[0][0];

        $validation = (new ValidateHeadings())->execute($headings, $type);

        if (! $validation['passed']) {
            $message = "Invalid File: The following headings are missing: {$validation['missing']}.";

            $result['passed'] = false;
            $result['message'] = $message;

            return $result;
        }

        if (self::inQueue($type, $fileName)) {
            $message = 'Excel file already uploaded';

            $result['passed'] = false;
            $result['message'] = $message;

            return $result;
        }

        return $result;
    }

    /** @param \Illuminate\Support\Collection<int, \App\Models\ExcelImportEvent> $importEvents */
    public static function updateStatues(Collection $importEvents, ImportEventStatus $status): void
    {
        foreach ($importEvents as $importEvent) {
            $importEvent->updateStatus($status);
        }
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\RawFinalResult, \App\Models\ExcelImportEvent> */
    public function rawFinalResults(): HasMany
    {
        return $this->hasMany(RawFinalResult::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\RawExcelResult, \App\Models\ExcelImportEvent> */
    public function rawExcelResults(): HasMany
    {
        return $this->hasMany(RawExcelResult::class);
    }

    /**
     * phpcs:ignore SlevomatCodingStandard.Files.LineLength
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\RawCurriculumCourse, \App\Models\ExcelImportEvent>
     */
    public function rawCurriculumCourses(): HasMany
    {
        return $this->hasMany(RawCurriculumCourse::class);
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
        $this->message = $message;
        $this->status = ImportEventStatus::FAILED;
        $this->save();
    }

    /** @return \Illuminate\Support\Collection<int, string> */
    public function getUniqueRegistrationNumbers(): Collection
    {
        return $this->rawFinalResults()
            ->orderBy('registration_number')
            ->distinct()
            ->pluck('registration_number');
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

    /** @return array{data: 'array', status: 'App\Enums\ImportEventStatus', type: 'App\Enums\ExcelImportType' } */
    protected function casts(): array
    {
        return [
            'data' => 'array',
            'status' => ImportEventStatus::class,
            'type' => ExcelImportType::class,
        ];
    }
}
