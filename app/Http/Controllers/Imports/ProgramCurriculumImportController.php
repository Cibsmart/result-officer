<?php

declare(strict_types=1);

namespace App\Http\Controllers\Imports;

use App\Data\Department\DepartmentListData;
use App\Data\Imports\ExcelImportEventListData;
use App\Enums\ExcelImportType;
use App\Enums\ImportEventStatus;
use App\Http\Requests\Imports\CurriculumImportRequest;
use App\Models\ExcelImportEvent;
use App\Models\User;
use App\ViewModels\Imports\CurriculumImportPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

final class ProgramCurriculumImportController
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        assert($user instanceof User);

        return Inertia::render('programCurriculum/import/page', new CurriculumImportPage(
            data: ExcelImportEventListData::forUser($user, ExcelImportType::CURRICULUM),
            departments: DepartmentListData::forUser($user),
        ));
    }

    public function store(CurriculumImportRequest $request): RedirectResponse
    {
        $file = $request->validated()['file'];

        $type = ExcelImportType::CURRICULUM;

        $fileName = $file->getClientOriginalName();

        $checks = ExcelImportEvent::validateExcelFileHeadingsAndCheckQueue($file, $type, $fileName);

        if (! $checks['passed']) {
            return redirect()->back()->error($checks['message']);
        }

        $filePath = Storage::putFile($type->value, $file);
        assert(is_string($filePath));

        ExcelImportEvent::new(
            user: $request->user(), type: $type, filePath: $filePath, fileName: $fileName,
            data: ['program_id' => $request->input('program.id')],
        );

        return redirect()->back()->success('File uploaded and queued for processing.');
    }

    public function delete(ExcelImportEvent $event): RedirectResponse
    {
        if ($event->status === ImportEventStatus::COMPLETED) {
            return redirect()->back()->warning('Completed Import cannot be deleted.');
        }

        Storage::delete($event->file_path);
        $event->delete();

        return redirect()->back()->info('Import deleted.');
    }
}
