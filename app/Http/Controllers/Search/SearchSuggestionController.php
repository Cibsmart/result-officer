<?php

declare(strict_types=1);

namespace App\Http\Controllers\Search;

use App\Models\User;
use App\Services\Search\GlobalSearch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class SearchSuggestionController
{
    public function __invoke(Request $request, GlobalSearch $search): JsonResponse
    {
        $user = $request->user();
        assert($user instanceof User);

        $query = $request->string('q')->trim()->value();

        return response()->json(['groups' => $search->handle($user, $query)]);
    }
}
