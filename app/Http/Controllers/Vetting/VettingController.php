<?php

declare(strict_types=1);

namespace App\Http\Controllers\Vetting;

use App\Models\Student;
use App\Models\User;
use App\Models\VettingEvent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

use function Illuminate\Support\defer;

final class VettingController
{
    public function create(Student $student, Request $request): RedirectResponse
    {
        $user = $request->user();
        assert($user instanceof User);

        $vettingEvent = VettingEvent::getOrCreateUsingStudent($student, $user);

        defer(fn () => Artisan::call('rp:vet', ['vettingEventId' => $vettingEvent->id]));

        return redirect()->back()->success("Vetting Started for {$student->registration_number}");
    }
}
