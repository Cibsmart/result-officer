<?php

declare(strict_types=1);

namespace App\Http\Controllers\Imports;

use App\Data\Imports\ExcelImportEventListData;
use App\Enums\ExcelImportType;
use App\Enums\ImportEventStatus;
use App\Models\ExcelImportEvent;
use App\Models\User;
use App\ViewModels\Imports\ExcelImportPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

final class FinalResultImportController
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        assert($user instanceof User);

        return Inertia::render('finalResults/import/page', new ExcelImportPage(
            data: ExcelImportEventListData::forUser($user, ExcelImportType::FINAL_RESULT),
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $file = $request->validate(['file' => ['required', 'file', 'mimes:xlsx']])['file'];

        $type = ExcelImportType::FINAL_RESULT;

        $fileName = $file->getClientOriginalName();

        $checks = ExcelImportEvent::validateExcelFileHeadingsAndCheckQueue($file, $type, $fileName);

        if (! $checks['passed']) {
            return redirect()->back()->error($checks['message']);
        }

        $filePath = Storage::putFile($type->value, $file);
        assert(is_string($filePath));

        ExcelImportEvent::new($request->user(), $type, $filePath, $fileName);

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
