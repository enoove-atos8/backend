<?php

namespace Infrastructure\Repositories\AI\Search;

use App\Domain\AI\Search\DataTransferObjects\AiSearchHistoryData;
use App\Domain\AI\Search\Interfaces\AiSearchHistoryRepositoryInterface;
use App\Domain\AI\Search\Models\AiSearchHistory;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Infrastructure\Repositories\BaseRepository;

class AiSearchHistoryRepository extends BaseRepository implements AiSearchHistoryRepositoryInterface
{
    public mixed $model = AiSearchHistory::class;

    const TABLE_NAME = 'ai_search_history';

    const ID_COLUMN = 'id';

    const USER_ID_COLUMN = 'user_id';

    const SUCCESS_COLUMN = 'success';

    const CREATED_AT_COLUMN = 'created_at';

    const DISPLAY_SELECT_COLUMNS = [
        'ai_search_history.id',
        'ai_search_history.user_id',
        'ai_search_history.question',
        'ai_search_history.sql_generated',
        'ai_search_history.result_data',
        'ai_search_history.result_title',
        'ai_search_history.result_description',
        'ai_search_history.suggested_followup',
        'ai_search_history.execution_time_ms',
        'ai_search_history.success',
        'ai_search_history.error_message',
        'ai_search_history.created_at',
    ];

    /**
     * {@inheritDoc}
     */
    public function save(AiSearchHistoryData $data): AiSearchHistoryData
    {
        $created = $this->create([
            'user_id' => $data->userId,
            'question' => $data->question,
            'sql_generated' => $data->sqlGenerated,
            'result_data' => $data->resultData,
            'result_title' => $data->resultTitle,
            'result_description' => $data->resultDescription,
            'suggested_followup' => $data->suggestedFollowup,
            'execution_time_ms' => $data->executionTimeMs,
            'success' => $data->success,
            'error_message' => $data->errorMessage,
        ]);

        return AiSearchHistoryData::fromResponse($created->toArray());
    }

    /**
     * {@inheritDoc}
     *
     * @throws BindingResolutionException
     */
    public function getRecentByUserId(int $userId, int $limit = 10): Collection
    {
        $query = function () use ($userId, $limit) {
            $result = DB::table(self::TABLE_NAME)
                ->select(self::DISPLAY_SELECT_COLUMNS)
                ->where(self::USER_ID_COLUMN, BaseRepository::OPERATORS['EQUALS'], $userId)
                ->orderByDesc(self::CREATED_AT_COLUMN)
                ->limit($limit)
                ->get();

            return collect($result)->map(fn ($item) => AiSearchHistoryData::fromResponse((array) $item));
        };

        return $this->doQuery($query);
    }

    /**
     * {@inheritDoc}
     *
     * @throws BindingResolutionException
     */
    public function getSuccessfulByUserId(int $userId, int $limit = 50): Collection
    {
        $query = function () use ($userId, $limit) {
            $result = DB::table(self::TABLE_NAME)
                ->select(self::DISPLAY_SELECT_COLUMNS)
                ->where(self::USER_ID_COLUMN, BaseRepository::OPERATORS['EQUALS'], $userId)
                ->where(self::SUCCESS_COLUMN, BaseRepository::OPERATORS['EQUALS'], true)
                ->orderByDesc(self::CREATED_AT_COLUMN)
                ->limit($limit)
                ->get();

            return collect($result)->map(fn ($item) => AiSearchHistoryData::fromResponse((array) $item));
        };

        return $this->doQuery($query);
    }

    /**
     * {@inheritDoc}
     */
    public function executeQuery(string $sql): array
    {
        $result = DB::select($sql);

        return array_map(fn ($item) => (array) $item, $result);
    }
}
