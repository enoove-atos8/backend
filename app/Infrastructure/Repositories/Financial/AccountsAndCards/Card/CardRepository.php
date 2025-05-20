<?php

namespace Infrastructure\Repositories\Financial\AccountsAndCards\Card;

use Domain\Financial\AccountsAndCards\Cards\DataTransferObjects\CardData;
use Domain\Financial\AccountsAndCards\Cards\Interfaces\CardRepositoryInterface;
use Domain\Financial\AccountsAndCards\Cards\Models\Card;
use Domain\Financial\Movements\Models\Movement;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Infrastructure\Repositories\BaseRepository;

class CardRepository extends BaseRepository implements CardRepositoryInterface
{

    protected mixed $model = Card::class;

    const TABLE_NAME = 'cards';
    const ID_COLUMN = 'cards.id';
    const DELETED_COLUMN = 'deleted';
    const GROUP_ID_COLUMN = 'group_id';
    const NAME_COLUMN = 'name';
    const DESCRIPTION_COLUMN = 'description';
    const CARD_NUMBER_COLUMN = 'card_number';
    const EXPIRY_DATE_COLUMN = 'expiry_date';
    const CLOSING_DATE_COLUMN = 'closing_date';
    const STATUS_COLUMN = 'status';
    const ACTIVE_COLUMN = 'active';
    const CREDIT_CARD_BRAND_COLUMN = 'credit_card_brand';

    const DISPLAY_SELECT_COLUMNS = [
        'cards.'
    ];


    /**
     * Array of conditions
     */
    private array $queryConditions = [];


    /**
     * Save a new card or update an existing one.
     *
     * @param CardData $cardData
     * @return Card
     */
    public function saveCard(CardData $cardData): Card
    {
        return $this->create([
            'name' => $cardData->name,
            'description' => $cardData->description,
            'card_number' => $cardData->cardNumber,
            'expiry_date' => $cardData->expiryDate,
            'closing_date' => $cardData->closingDate,
            'status' => $cardData->status,
            'active' => $cardData->active,
            'deleted' => $cardData->deleted,
            'credit_card_brand' => $cardData->creditCardBrand,
            'person_type' => $cardData->personType,
            'card_holder_name' => $cardData->cardHolderName,
            'limit' => $cardData->limit,
        ]);
    }



    /**
     * Get all cards from the database.
     *
     * @return Collection
     * @throws BindingResolutionException
     */
    public function getCards(): Collection
    {
        $query = function () {

            $q = DB::table(CardRepository::TABLE_NAME)
                ->where(self::DELETED_COLUMN, BaseRepository::OPERATORS['EQUALS'], 0)
                ->orderBy(self::ID_COLUMN);


            $result = $q->get();
            return collect($result)->map(fn($item) => CardData::fromResponse((array) $item));
        };

        return $this->doQuery($query);
    }



    /**
     * Get a specific card by ID.
     *
     * @param int $id
     * @return object|null
     */
    public function getCardById(int $id): ?CardData
    {
        //TODO: Implements here
    }



    /**
     * Delete a card by ID.
     *
     * @param int $id
     * @return bool
     * @throws BindingResolutionException
     */
    public function deleteCard(int $id): bool
    {
        $conditions = [
            'field' => self::ID_COLUMN,
            'operator' => BaseRepository::OPERATORS['EQUALS'],
            'value' => $id,
        ];

        return $this->update($conditions, [
            'deleted' =>   1,
        ]);
    }
}
