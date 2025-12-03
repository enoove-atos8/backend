<?php

namespace App\Domain\AI\Search\Actions;

use App\Domain\AI\Search\Interfaces\AiSearchHistoryRepositoryInterface;
use Illuminate\Support\Collection;

class GetSearchSuggestionsAction
{
    private AiSearchHistoryRepositoryInterface $historyRepository;

    public function __construct(AiSearchHistoryRepositoryInterface $historyRepository)
    {
        $this->historyRepository = $historyRepository;
    }

    public function execute(int $userId, int $limit = 50): Collection
    {
        return $this->historyRepository->getSuccessfulByUserId($userId, $limit);
    }
}
