<?php

namespace Infrastructure\Repositories\Financial\AccountsAndCards\Card;


use App\Domain\Financial\Exits\Purchases\DataTransferObjects\CardInvoiceData;
use App\Domain\Financial\Exits\Purchases\Interfaces\CardInvoiceRepositoryInterface;
use App\Domain\Financial\Exits\Purchases\Models\CardInvoice;
use DateTime;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Infrastructure\Repositories\BaseRepository;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class CardInvoiceRepository extends BaseRepository implements CardInvoiceRepositoryInterface
{

    protected mixed $model = CardInvoice::class;

    const TABLE_NAME = 'cards_invoices';

    const ID_COLUMN = 'id';
    const CARD_ID_COLUMN = 'card_id';
    const STATUS_COLUMN = 'status';
    const REFERENCE_DATE_COLUMN = 'reference_date';
    const DELETED_COLUMN = 'deleted';
    const INVOICE_CLOSED_VALUE = 'closed';
    const INVOICE_OPEN_VALUE = 'open';
    const INVOICE_LATE_VALUE = 'late';
    const INVOICE_PAID_VALUE = 'paid';


    const DISPLAY_SELECT_COLUMNS = [
        'cards_invoices.id as cards_invoices_id',
        'cards_invoices.card_id as cards_invoices_card_id',
        'cards_invoices.status as cards_invoices_status',
        'cards_invoices.amount as cards_invoices_amount',
        'cards_invoices.reference_date as cards_invoices_reference_date',
        'cards_invoices.is_closed as cards_invoices_is_closed',
        'cards_invoices.deleted as cards_invoices_deleted',
    ];


    /**
     * Array of conditions
     */
    private array $queryConditions = [];


    /**
     * @param int $cardId
     * @param string $referenceDate
     * @return CardInvoiceData|null
     * @throws BindingResolutionException
     * @throws Exception
     */
    public function getInvoiceByCardIdAndDate(int $cardId, string $referenceDate): ?CardInvoiceData
    {
        $referenceDate = (new DateTime($referenceDate))->format('Y-m');

        $result = $this->model
            ->where(self::DELETED_COLUMN, BaseRepository::OPERATORS['EQUALS'], 0)
            ->where(self::CARD_ID_COLUMN, BaseRepository::OPERATORS['EQUALS'], $cardId)
            ->where(self::REFERENCE_DATE_COLUMN, BaseRepository::OPERATORS['LIKE'], "%{$referenceDate}%")
            ->first();

        if (!$result) {
            return null;
        }

        $attributes = $result->getAttributes();
        return CardInvoiceData::fromResponse($attributes);
    }


    /**
     * @param int $invoiceId
     * @return CardInvoiceData|null
     * @throws UnknownProperties
     */
    public function getInvoiceById(int $invoiceId): ?CardInvoiceData
    {
        $result = $this->model
            ->where(self::DELETED_COLUMN, BaseRepository::OPERATORS['EQUALS'], 0)
            ->where(self::ID_COLUMN, BaseRepository::OPERATORS['EQUALS'], $invoiceId)
            ->first();

        if (!$result) {
            return null;
        }

        $attributes = $result->getAttributes();
        return CardInvoiceData::fromResponse($attributes);
    }


    /**
     * @param int $cardId
     * @param int $invoiceId
     * @return CardInvoiceData|null
     * @throws UnknownProperties
     */
    public function getInvoiceIndicators(int $cardId, int $invoiceId): ?array
    {
        $result = $this->model
            ->where(self::DELETED_COLUMN, BaseRepository::OPERATORS['EQUALS'], 0)
            ->where(self::CARD_ID_COLUMN, BaseRepository::OPERATORS['EQUALS'], $cardId)
            ->where(self::ID_COLUMN, BaseRepository::OPERATORS['EQUALS'], $invoiceId)
            ->first();

        if (!$result) {
            return null;
        }

        $attributes = $result->getAttributes();
        return CardInvoiceData::fromResponse($attributes);
    }




    /**
     * @param CardInvoiceData $cardInvoiceData
     * @return CardInvoiceData
     * @throws UnknownProperties
     */
    public function createInvoice(CardInvoiceData $cardInvoiceData): CardInvoiceData
    {
        $created = $this->create([
            'card_id'           =>  $cardInvoiceData->cardId,
            'status'            =>  $cardInvoiceData->status,
            'amount'            =>  $cardInvoiceData->amount,
            'reference_date'    =>  $cardInvoiceData->referenceDate,
            'payment_date'      =>  $cardInvoiceData->paymentDate,
            'payment_method'    =>  $cardInvoiceData->paymentMethod,
            'is_closed'         =>  $cardInvoiceData->isClosed,
            'deleted'           =>  $cardInvoiceData->deleted,
        ]);

        return CardInvoiceData::fromResponse($created->toArray());
    }


    /**
     * @param int $invoiceId
     * @param float $amount
     * @return void
     * @throws BindingResolutionException
     */
    public function updateInvoiceAmount(int $invoiceId, float $amount): void
    {
        $conditions = [
            'field' => self::ID_COLUMN,
            'operator' => BaseRepository::OPERATORS['EQUALS'],
            'value' => $invoiceId,
        ];

        $this->update($conditions, [
            'amount' =>   $amount,
        ]);
    }


    /**
     * @param int $invoiceId
     * @param string $status
     * @return void
     * @throws BindingResolutionException
     */
    public function updateInvoiceStatus(int $invoiceId, string $status): void
    {
        $conditions = [
            'field' => self::ID_COLUMN,
            'operator' => BaseRepository::OPERATORS['EQUALS'],
            'value' => $invoiceId,
        ];

        $this->update($conditions, [
            'status' =>   $status,
        ]);
    }


    /**
     * @param int $cardId
     * @param bool $getClosedInvoices
     * @return CardInvoiceData|null
     * @throws BindingResolutionException
     */
    public function getInvoicesByCardId(int $cardId, bool $getClosedInvoices = false): ?Collection
    {
        $query = function () use ($cardId, $getClosedInvoices) {

            $q = DB::table(self::TABLE_NAME)
                ->where(self::DELETED_COLUMN, BaseRepository::OPERATORS['EQUALS'], 0)
                ->where(self::CARD_ID_COLUMN, BaseRepository::OPERATORS['EQUALS'], $cardId)
                ->orderBy(self::REFERENCE_DATE_COLUMN, BaseRepository::ORDERS['ASC']);

            if($getClosedInvoices)
                $q->whereIn(self::STATUS_COLUMN, [self::INVOICE_CLOSED_VALUE, self::INVOICE_LATE_VALUE]);

            $result = $q->get();
            return collect($result)->map(fn($item) => CardInvoiceData::fromResponse((array) $item));
        };

        return $this->doQuery($query);
    }


    /**
     * @param int $cardId
     * @param string $status
     * @return CardInvoiceData|null
     * @throws BindingResolutionException
     */
    public function getInvoicesByCardIdAndStatus(int $cardId, string $status): ?Collection
    {
        $query = function () use ($cardId, $status) {

            $q = DB::table(self::TABLE_NAME)
                ->where(self::DELETED_COLUMN, BaseRepository::OPERATORS['EQUALS'], 0)
                ->where(self::CARD_ID_COLUMN, BaseRepository::OPERATORS['EQUALS'], $cardId)
                ->where(self::STATUS_COLUMN, BaseRepository::OPERATORS['EQUALS'], $status)
                ->orderBy(self::REFERENCE_DATE_COLUMN, BaseRepository::ORDERS['ASC']);


            $result = $q->get();
            return collect($result)->map(fn($item) => CardInvoiceData::fromResponse((array) $item));
        };

        return $this->doQuery($query);
    }
}
