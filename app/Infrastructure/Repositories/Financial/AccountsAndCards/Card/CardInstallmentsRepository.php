<?php

namespace App\Infrastructure\Repositories\Financial\AccountsAndCards\Card;


use App\Domain\Financial\Exits\Purchases\DataTransferObjects\CardInstallmentData;
use App\Domain\Financial\Exits\Purchases\DataTransferObjects\CardInvoiceData;
use App\Domain\Financial\Exits\Purchases\Interfaces\CardInstallmentsRepositoryInterface;
use App\Domain\Financial\Exits\Purchases\Models\CardInstallment;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Infrastructure\Repositories\BaseRepository;
use Infrastructure\Repositories\Ecclesiastical\Groups\GroupsRepository;
use Infrastructure\Repositories\Financial\AccountsAndCards\Card\CardInvoiceRepository;
use Infrastructure\Repositories\Financial\AccountsAndCards\Card\CardPurchaseRepository;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class CardInstallmentsRepository extends BaseRepository implements CardInstallmentsRepositoryInterface
{

    protected mixed $model = CardInstallment::class;

    const TABLE_NAME = 'cards_installments';

    const ID_COLUMN = 'id';
    const CARD_ID_COLUMN = 'card_id';

    const PURCHASE_ID_COLUMN = 'purchase_id';
    const INVOICE_ID_COLUMN = 'invoice_id';
    const GROUP_ID_COLUMN = 'group_id';
    const INSTALLMENT_ID_COLUMN = 'installment_id';
    const DATE_COLUMN = 'date';
    const DELETED_COLUMN = 'deleted';

    const DISPLAY_SELECT_COLUMNS = [
        'cards_installments.id as cards_installments_id',
        'cards_installments.invoice_id as cards_installments_invoice_id',
        'cards_installments.purchase_id as cards_installments_purchase_id',
        'cards_installments.status as cards_installments_status',
        'cards_installments.amount as cards_installments_amount',
        'cards_installments.installment as cards_installments_installment',
        'cards_installments.installment_amount as cards_installments_installment_amount',
        'cards_installments.date as cards_installments_date',
        'cards_installments.deleted as cards_installments_deleted',
    ];


    /**
     * Array of conditions
     */
    private array $queryConditions = [];


    /**
     * @param int $cardId
     * @param string $date
     * @return CardInvoiceData|null
     * @throws BindingResolutionException
     */
    public function getInstallmentsWithPurchase(int $cardId, string $date): ?Collection
    {
        $displayColumnsFromRelationship = array_merge(self::DISPLAY_SELECT_COLUMNS,
            CardPurchaseRepository::DISPLAY_SELECT_COLUMNS,
            CardInvoiceRepository::DISPLAY_SELECT_COLUMNS,
            GroupsRepository::DISPLAY_SELECT_COLUMNS,
        );

        $query = function () use ($cardId, $date, $displayColumnsFromRelationship) {

            $q = DB::table(self::TABLE_NAME)
                ->select($displayColumnsFromRelationship)
                ->leftJoin(CardPurchaseRepository::TABLE_NAME, self::TABLE_NAME . '.' . self::PURCHASE_ID_COLUMN,
                    BaseRepository::OPERATORS['EQUALS'],
                    CardPurchaseRepository::TABLE_NAME . '.' . CardPurchaseRepository::ID_COLUMN)

                ->leftJoin(CardInvoiceRepository::TABLE_NAME, self::TABLE_NAME . '.' . self::INVOICE_ID_COLUMN,
                    BaseRepository::OPERATORS['EQUALS'],
                    CardInvoiceRepository::TABLE_NAME . '.' . CardInvoiceRepository::ID_COLUMN)

                ->leftJoin(GroupsRepository::TABLE_NAME, CardPurchaseRepository::TABLE_NAME . '.' . self::GROUP_ID_COLUMN,
                    BaseRepository::OPERATORS['EQUALS'],
                    GroupsRepository::TABLE_NAME . '.' . GroupsRepository::ID_COLUMN)

                ->where(self::TABLE_NAME . '.' . self::DELETED_COLUMN, BaseRepository::OPERATORS['EQUALS'], 0)
                ->where(self::TABLE_NAME . '.' . self::CARD_ID_COLUMN, BaseRepository::OPERATORS['EQUALS'], $cardId)
                ->where(self::TABLE_NAME . '.' . self::DATE_COLUMN, BaseRepository::OPERATORS['EQUALS'], $date)
                ->orderBy(self::TABLE_NAME . '.' . self::ID_COLUMN);


            $result = $q->get();
            return $result->map(fn($item) => CardInstallmentData::fromResponse((array) $item));
        };

        return $this->doQuery($query);
    }


    /**
     * @param CardInstallmentData $cardInstallmentData
     * @return CardInstallmentData
     * @throws UnknownProperties
     */
    public function createInstallment(CardInstallmentData $cardInstallmentData): CardInstallmentData
    {
        $created = $this->create([
            'card_id'               =>  $cardInstallmentData->cardId,
            'invoice_id'            =>  $cardInstallmentData->cardInvoiceData->id,
            'purchase_id'           =>  $cardInstallmentData->cardPurchaseData->id,
            'status'                =>  $cardInstallmentData->status,
            'amount'                =>  $cardInstallmentData->amount,
            'installment'           =>  $cardInstallmentData->installment,
            'installment_amount'    =>  $cardInstallmentData->installmentAmount,
            'date'                  =>  $cardInstallmentData->date,
            'deleted'               =>  $cardInstallmentData->deleted,
        ]);

        return CardInstallmentData::fromSelf($created->toArray());
    }
}
