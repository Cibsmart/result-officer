<?php

declare(strict_types=1);

namespace App\ViewModels\Students;

use App\Data\Enums\StudentStatusListData;
use App\Data\Students\StudentComprehensiveData;
use Closure;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScriptType;

final class StudentShowPage extends Data
{
    public function __construct(
        #[TypeScriptType(StudentComprehensiveData::class)]
        public readonly Closure $data,
        public readonly StudentStatusListData $statues,
        public readonly int $selectedIndex = 0,
    ) {
    }
}
