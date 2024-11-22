<?php

namespace Infrastructure\Repositories\Financial\Entries\Cults;

use App\Domain\Financial\Entries\Cults\DataTransferObjects\CultData;
use App\Domain\Financial\Entries\Cults\Interfaces\CultRepositoryInterface;
use App\Domain\Financial\Entries\Cults\Models\Cult;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Infrastructure\Repositories\BaseRepository;

class CultRepository extends BaseRepository implements CultRepositoryInterface
{
    protected mixed $model = Cult::class;

    const TABLE_NAME = 'cults';



    /**
     * @param CultData $cultData
     * @return Cult
     */
    public function createCult(CultData $cultData): Cult
    {
        return $this->create([
            'reviewer_id'                       =>   $cultData->reviewerId,
            'cult_day'                          =>   $cultData->cultDay,
            'cult_date'                         =>   $cultData->cultDate,
            'date_transaction_compensation'     =>   $cultData->dateTransactionCompensation,
            'transaction_type'                  =>   $cultData->transactionType,
            'tithes_amount'                     =>   $cultData->amountTithes,
            'designated_amount'                 =>   $cultData->amountDesignated,
            'offers_amount'                     =>   $cultData->amountOffers,
            'deleted'                           =>   $cultData->deleted,
            'receipt'                           =>   $cultData->receipt,
            'comments'                          =>   null,
        ]);
    }

    /**
     * @return Collection
     */
    public function getCults(): Collection
    {
        // TODO: Implement getCults() method.
    }
}
