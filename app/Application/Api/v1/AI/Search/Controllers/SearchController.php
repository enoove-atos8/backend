<?php

namespace App\Application\Api\v1\AI\Search\Controllers;

use App\Application\Api\v1\AI\Search\Requests\SearchRequest;
use Application\Core\Http\Controllers\Controller;
use App\Domain\AI\Search\Actions\GetRecentSearchesAction;
use App\Domain\AI\Search\Actions\GetSearchSuggestionsAction;
use App\Domain\AI\Search\Actions\ProcessSearchQueryAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(
        SearchRequest $request,
        ProcessSearchQueryAction $action
    ): JsonResponse {
        $result = $action->execute($request->searchData());

        if (! $result->success) {
            return response()->json($result, 400);
        }

        return response()->json($result);
    }

    public function recent(
        Request $request,
        GetRecentSearchesAction $action
    ): JsonResponse {
        $result = $action->execute($request->user()->id);

        return response()->json($result);
    }

    public function suggestions(
        Request $request,
        GetSearchSuggestionsAction $action
    ): JsonResponse {
        $result = $action->execute($request->user()->id);

        return response()->json($result);
    }
}
