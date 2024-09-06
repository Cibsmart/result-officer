<?php

declare(strict_types=1);

namespace App\Http\Controllers\Reports;

use App\Data\Composite\CompositeSheetData;
use App\Data\Level\LevelListData;
use App\Data\Program\ProgramListData;
use App\Data\Semester\SemesterListData;
use App\Data\Session\SessionListData;
use App\Http\Requests\Composite\CompositeSheetRequest;
use App\Models\Level;
use App\Models\Program;
use App\Models\Semester;
use App\Models\Session;
use App\ViewModels\Reports\CompositeFormPage;
use App\ViewModels\Reports\CompositeViewPage;
use Illuminate\View\View;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\LaravelPdf\Facades\Pdf;
use Spatie\LaravelPdf\PdfBuilder;

final readonly class CompositeSheetController
{
    public function form(): Response
    {
        return Inertia::render('reports/composite/form/page', new CompositeFormPage(
            program: ProgramListData::new(),
            semester: SemesterListData::new(),
            session: SessionListData::new(),
            level: LevelListData::new(),
        ));
    }

    public function view(CompositeSheetRequest $request): Response
    {
        return Inertia::render('reports/composite/view/page', new CompositeViewPage(
            data: CompositeSheetData::fromModel(
                program: $request->input('program'),
                session: $request->input('session'),
                level: $request->input('level'),
                semester: $request->input('semester'),
            ),
        ));
    }

    public function print(
        Program $program,
        Session $session,
        Level $level,
        Semester $semester,
    ): View|Pdf|PdfBuilder {
        $data = CompositeSheetData::fromModel($program, $session, $level, $semester);

        return view('pdfs.composite.view', [
            'data' => $data,
        ]);

        //        return Pdf::view('pdfs.composite.view', ['data' => $data])
        //            ->withBrowsershot(static function (Browsershot $browsershot): void {
        //                $browsershot->setChromePath(Config::string('rp_pdf.chromium.path'));
        //                $browsershot->scale(0.80);
        //            })
        //            ->format(Format::Legal)
        //            ->landscape()
        //            ->margins(5, 5, 5, 5)
        //            ->name("{$data->program->name}_CompositeSheet.pdf");
    }
}
