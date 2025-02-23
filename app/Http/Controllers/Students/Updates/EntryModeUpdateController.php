<?php

declare(strict_types=1);

namespace App\Http\Controllers\Students\Updates;

use App\Actions\Students\Updates\EntryModelUpdateAction;
use App\Enums\EntryMode;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

final class EntryModeUpdateController
{
    public function __invoke(
        Student $student,
        Request $request,
        EntryModelUpdateAction $action,
    ): RedirectResponse {

        $validated = $request->validate([
            'entry_mode' => ['required', Rule::enum(EntryMode::class)],
            'remark' => ['required', 'string', 'min:3', 'max:255'],
        ]);

        $user = $request->user();
        assert($user instanceof User);

        $newValue = EntryMode::from($validated['entry_mode']);

        $action->execute($student, $newValue, $validated['remark'], $user);

        return redirect()
            ->route('students.show', ['student' => $student])
            ->success('Student entry mode updated successfully.');
    }
}
