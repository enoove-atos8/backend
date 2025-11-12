<?php

namespace Infrastructure\Repositories\Financial\Entries\Cults;

use App\Domain\Financial\Entries\Cults\DataTransferObjects\CultData;
use App\Domain\Financial\Entries\Cults\Interfaces\CultRepositoryInterface;
use App\Domain\Financial\Entries\Cults\Models\Cult;
use App\Infrastructure\Repositories\Financial\Reviewer\FinancialReviewerRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Infrastructure\Repositories\BaseRepository;

class CultRepository extends BaseRepository implements CultRepositoryInterface
{
    protected mixed $model = Cult::class;

    const TABLE_NAME = 'cults';
    const DELETED_COLUMN = 'deleted';
    const ID_COLUMN = 'cults.id';
    const PAGINATE_NUMBER = 30;
    const REVIEWER__ID_COLUMN = 'cults.reviewer_id';
    const DATE_TRANSACTION_COMPENSATION_COLUMN = 'cults.date_transaction_compensation';


    const DISPLAY_SELECT_COLUMNS = [
        'cults.id as cults_id',
        'cults.reviewer_id as cults_reviewer_id',
        'cults.worship_without_entries as cults_worship_without_entries',
        'cults.cult_day as cults_cult_day',
        'cults.cult_date as cults_cult_date',
        'cults.date_transaction_compensation as cults_date_transaction_compensation',
        'cults.account_id as cults_account_id',
        'cults.transaction_type as cults_transaction_type',
        'cults.tithes_amount as cults_tithes_amount',
        'cults.designated_amount as cults_designated_amount',
        'cults.offer_amount as cults_offer_amount',
        'cults.deleted as cults_deleted',
        'cults.receipt as cults_receipt',
        'cults.comments as cults_comments',
    ];


    /**
     * Array of conditions
     */
    private array $queryConditions = [];



    /**
     * @param CultData $cultData
     * @return Cult
     */
    public function createCult(CultData $cultData): Cult
    {
        return $this->create([
            'reviewer_id'                       =>   $cultData->reviewerId,
            'worship_without_entries'           =>   $cultData->worshipWithoutEntries,
            'cult_day'                          =>   $cultData->cultDay,
            'cult_date'                         =>   $cultData->cultDate,
            'date_transaction_compensation'     =>   $cultData->dateTransactionCompensation,
            'account_id'                        =>   $cultData->accountId,
            'transaction_type'                  =>   $cultData->transactionType,
            'tithes_amount'                     =>   $cultData->amountTithes != null ? $cultData->amountTithes : 0,
            'designated_amount'                 =>   $cultData->amountDesignated != null ? $cultData->amountDesignated : 0,
            'offer_amount'                     =>   $cultData->amountOffer != null ? $cultData->amountOffer : 0,
            'deleted'                           =>   $cultData->deleted,
            'receipt'                           =>   $cultData->receipt,
            'comments'                          =>   null,
        ]);
    }


    /**
     * @param $id
     * @param CultData $cultData
     * @return bool
     * @throws BindingResolutionException
     */
    public function updateCult($id, CultData $cultData): mixed
    {
        $conditions = [
            'field' => self::ID_COLUMN,
            'operator' => BaseRepository::OPERATORS['EQUALS'],
            'value' => $id,];

        return $this->update($conditions, [
            'reviewer_id'                       =>   $cultData->reviewerId,
            'worship_without_entries'           =>   $cultData->worshipWithoutEntries,
            'cult_day'                          =>   $cultData->cultDay,
            'cult_date'                         =>   $cultData->cultDate,
            'date_transaction_compensation'     =>   $cultData->dateTransactionCompensation,
            'account_id'                        =>   $cultData->accountId,
            'transaction_type'                  =>   $cultData->transactionType,
            'tithes_amount'                     =>   $cultData->amountTithes != null ? $cultData->amountTithes : 0,
            'designated_amount'                 =>   $cultData->amountDesignated != null ? $cultData->amountDesignated : 0,
            'offer_amount'                      =>   $cultData->amountOffer != null ? $cultData->amountOffer : 0,
            'deleted'                           =>   $cultData->deleted,
            'receipt'                           =>   $cultData->receipt,
            'comments'                          =>   null,
        ]);
    }


    /**
     * @param bool $paginate
     * @param string|null $dates
     * @return Collection|Paginator
     * @throws BindingResolutionException
     */
    public function getCults(bool $paginate = true, ?string $dates = null): Collection | Paginator
    {
        $displayColumnsFromRelationship = array_merge(self::DISPLAY_SELECT_COLUMNS,
            FinancialReviewerRepository::DISPLAY_SELECT_COLUMNS
        );

        $query = function () use ($paginate, $displayColumnsFromRelationship, $dates) {
            $q = DB::table(CultRepository::TABLE_NAME)
                ->select($displayColumnsFromRelationship)
                ->leftJoin(
                    FinancialReviewerRepository::TABLE_NAME,
                    self::REVIEWER__ID_COLUMN,
                    BaseRepository::OPERATORS['EQUALS'],
                    FinancialReviewerRepository::ID_COLUMN_JOINED)
                ->orderBy(self::ID_COLUMN, BaseRepository::ORDERS['DESC']);

            // Aplicar filtro de datas se fornecido
            if ($dates !== null && $dates !== 'all') {
                $arrDates = explode(',', $dates);
                $q->where(function($query) use ($arrDates) {
                    foreach ($arrDates as $date) {
                        $query->orWhere(self::DATE_TRANSACTION_COMPENSATION_COLUMN, BaseRepository::OPERATORS['LIKE'], "%{$date}%");
                    }
                });
            }

            if($paginate)
                return $q->simplePaginate(self::PAGINATE_NUMBER);
            else
                return $q->get();
        };

        return $this->doQuery($query);
    }


    /**
     * @param int $id
     * @return Model
     * @throws BindingResolutionException
     */
    public function getCultById(int $id): Model
    {
        $this->queryConditions = [];
        $this->queryConditions [] = $this->whereEqual(self::DELETED_COLUMN, 0, 'and');
        $this->queryConditions [] = $this->whereEqual(self::ID_COLUMN, $id, 'and');

        return $this->getItemWithRelationshipsAndWheres(
            $this->queryConditions,
            self::ID_COLUMN,
            ['*'],
            BaseRepository::ORDERS['DESC']
        );
    }
}
