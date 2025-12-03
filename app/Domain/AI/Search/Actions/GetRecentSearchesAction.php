<?php

namespace App\Domain\AI\Search\Actions;

use App\Domain\AI\Search\Interfaces\AiSearchHistoryRepositoryInterface;
use Illuminate\Support\Collection;

class GetRecentSearchesAction
{
    private AiSearchHistoryRepositoryInterface $historyRepository;

    public function __construct(AiSearchHistoryRepositoryInterface $historyRepository)
    {
        $this->historyRepository = $historyRepository;
    }

    public function execute(int $userId, int $limit = 10): Collection
    {
        return $this->historyRepository->getRecentByUserId($userId, $limit);
    }
}
