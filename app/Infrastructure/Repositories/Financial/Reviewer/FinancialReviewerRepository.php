<?php

namespace App\Infrastructure\Repositories\Financial\Reviewer;

use App\Domain\Financial\Reviewers\Interfaces\FinancialReviewerRepositoryInterface;
use App\Domain\Financial\Reviewers\Models\FinancialReviewer;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Infrastructure\Repositories\BaseRepository;

class FinancialReviewerRepository extends BaseRepository implements FinancialReviewerRepositoryInterface
{
    protected mixed $model = FinancialReviewer::class;

    const TABLE_NAME = 'financial_reviewers';

    const ID_COLUMN = 'id';

    const ID_COLUMN_JOINED = 'financial_reviewers.id';

    const DELETED_COLUMN = 'deleted';

    const ACTIVATED_COLUMN = 'activated';

    const FULL_NAME_COLUMN = 'full_name';

    const DISPLAY_SELECT_COLUMNS = [
        'financial_reviewers.id as financial_reviewers_id',
        'financial_reviewers.full_name as financial_reviewers_full_name',
        'financial_reviewers.reviewer_type as financial_reviewers_reviewer_type',
        'financial_reviewers.avatar as financial_reviewers_avatar',
        'financial_reviewers.gender as financial_reviewers_gender',
        'financial_reviewers.cpf as financial_reviewers_cpf',
        'financial_reviewers.rg as financial_reviewers_rg',
        'financial_reviewers.email as financial_reviewers_email',
        'financial_reviewers.cell_phone as financial_reviewers_cell_phone',
        'financial_reviewers.activated as financial_reviewers_activated',
        'financial_reviewers.deleted as financial_reviewers_deleted',
    ];

    /**
     * Array of conditions
     */
    private array $queryConditions = [];

    /**
     * @throws BindingResolutionException
     */
    public function getFinancialReviewers(): Collection
    {
        $this->queryConditions = [];
        $this->queryConditions[] = $this->whereEqual(self::DELETED_COLUMN, false, 'and');
        $this->queryConditions[] = $this->whereEqual(self::ACTIVATED_COLUMN, true, 'and');

        return $this->getItemsWithRelationshipsAndWheres($this->queryConditions, self::FULL_NAME_COLUMN, BaseRepository::ORDERS['ASC']);
    }

    /**
     * @throws BindingResolutionException
     */
    public function getReviewer(): ?Model
    {
        return $this->getItemByColumn(
            self::DELETED_COLUMN,
            BaseRepository::OPERATORS['EQUALS'],
            false
        );
    }

    /**
     * @param  FinancialReviewerData[]  $reviewersData
     */
    public function batchCreateReviewers(array $reviewersData): bool
    {
        $data = array_map(function ($reviewerData) {
            return [
                'full_name' => $reviewerData->fullName,
                'reviewer_type' => $reviewerData->reviewerType,
                'avatar' => $reviewerData->avatar,
                'gender' => $reviewerData->gender,
                'cpf' => $reviewerData->cpf ?: null,
                'rg' => $reviewerData->rg ?: null,
                'email' => $reviewerData->email ? strtolower($reviewerData->email) : null,
                'cell_phone' => $reviewerData->cellPhone,
                'activated' => $reviewerData->activated ?? true,
                'deleted' => $reviewerData->deleted ?? false,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $reviewersData);

        return DB::table(self::TABLE_NAME)->insert($data);
    }

    public function deleteReviewer(int $id): bool
    {
        return DB::table(self::TABLE_NAME)
            ->where(self::ID_COLUMN, $id)
            ->delete() > 0;
    }
}
