<?php

namespace Infrastructure\Repositories\Financial\Entries\Cults;

use App\Domain\Financial\Entries\Cults\DataTransferObjects\CultData;
use App\Domain\Financial\Entries\Cults\Interfaces\CultRepositoryInterface;
use App\Domain\Financial\Entries\Cults\Models\Cult;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Infrastructure\Repositories\BaseRepository;

class CultRepository extends BaseRepository implements CultRepositoryInterface
{
    protected mixed $model = Cult::class;

    const TABLE_NAME = 'cults';
    const DELETED_COLUMN = 'deleted';
    const ENTRIES_CULT_ID_COLUMN = 'entries.cult_id';


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
     * @return Collection
     * @throws BindingResolutionException
     */
    public function getCults(): Collection
    {
        $this->requiredRelationships = ['reviewer'];

        $this->queryConditions = [];
        $this->queryConditions [] = $this->whereEqual(self::DELETED_COLUMN, 0, 'and');

        return $this->getItemsWithRelationshipsAndWheres(
            $this->queryConditions,
            self::ID_COLUMN,
            BaseRepository::ORDERS['DESC']
        );
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
