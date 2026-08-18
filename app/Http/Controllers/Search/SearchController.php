<?php

declare(strict_types=1);

namespace App\Http\Controllers\Search;

use App\Models\User;
use App\Services\Search\GlobalSearch;
use App\ViewModels\Search\SearchPage;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class SearchController
{
    public function __invoke(Request $request, GlobalSearch $search): Response
    {
        $user = $request->user();
        assert($user instanceof User);

        $query = $request->string('q')->trim()->value();

        return Inertia::render('search/page', new SearchPage(
            query: $query,
            groups: $search->handle($user, $query, perGroup: 20),
        ));
    }
}
