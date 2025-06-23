<?php

namespace Infrastructure\Repositories\Financial\AccountsAndCards\Card;

use App\Domain\Financial\Exits\Purchases\DataTransferObjects\CardPurchaseData;
use App\Domain\Financial\Exits\Purchases\Interfaces\CardPurchaseRepositoryInterface;
use App\Domain\Financial\Exits\Purchases\Models\CardPurchase;
use Domain\Financial\Movements\Models\Movement;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Infrastructure\Repositories\BaseRepository;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class CardPurchaseRepository extends BaseRepository implements CardPurchaseRepositoryInterface
{

    protected mixed $model = CardPurchase::class;

    const TABLE_NAME = 'cards_purchases';

    const ID_COLUMN = 'id';

    const CARD_ID_COLUMN = 'card_id';
    const INVOICE_ID_COLUMN = 'invoice_id';
    const PURCHASE_DATE_COLUMN = 'purchase_date';
    const PENDING_VALUE = 'pending';
    const INVOICED_VALUE = 'invoiced';



    const DISPLAY_SELECT_COLUMNS = [
        'cards_purchases.id as cards_purchases_id',
        'cards_purchases.card_id as cards_purchases_card_id',
        'cards_purchases.status as cards_purchases_status',
        'cards_purchases.amount as cards_purchases_amount',
        'cards_purchases.installments as cards_purchases_installments',
        'cards_purchases.installment_amount as cards_purchases_installment_amount',
        'cards_purchases.establishment_name as cards_purchases_establishment_name',
        'cards_purchases.purchase_description as cards_purchases_purchase_description',
        'cards_purchases.date as cards_purchases_date',
        'cards_purchases.deleted as cards_purchases_deleted',
        'cards_purchases.receipt as cards_purchases_receipt',
    ];


    /**
     * Array of conditions
     */
    private array $queryConditions = [];


    /**
     * @param CardPurchaseData $cardPurchaseData
     * @return CardPurchaseData
     * @throws UnknownProperties
     */
    public function createPurchase(CardPurchaseData $cardPurchaseData): CardPurchaseData
    {
        $created = $this->create([
            'card_id'               =>  $cardPurchaseData->cardId,
            'status'                =>  $cardPurchaseData->status,
            'amount'                =>  $cardPurchaseData->amount,
            'installments'          =>  $cardPurchaseData->installments,
            'installment_amount'    =>  $cardPurchaseData->installmentAmount,
            'establishment_name'    =>  $cardPurchaseData->establishmentName,
            'purchase_description'  =>  $cardPurchaseData->purchaseDescription,
            'date'                  =>  $cardPurchaseData->date,
            'deleted'               =>  $cardPurchaseData->deleted,
            'receipt'               =>  $cardPurchaseData->receipt,
        ]);

        return CardPurchaseData::fromResponse($created->toArray());
    }


    /**
     * @param int $cardId
     * @param string $date
     * @return CardPurchaseData|null
     * @throws BindingResolutionException
     */
    public function getPurchases(int $cardId, string $date): ?Collection
    {
        $query = function () use ($cardId, $date) {

            $q = DB::table(self::TABLE_NAME)
                ->where(self::CARD_ID_COLUMN, BaseRepository::OPERATORS['EQUALS'], $cardId)
                ->where(self::PURCHASE_DATE_COLUMN, BaseRepository::OPERATORS['LIKE'], "%{$date}%")
                ->orderBy(self::ID_COLUMN, BaseRepository::ORDERS['DESC']);


            $result = $q->get();
            return collect($result)->map(fn($item) => CardPurchaseData::fromResponse((array) $item));
        };

        return $this->doQuery($query);
    }
}
