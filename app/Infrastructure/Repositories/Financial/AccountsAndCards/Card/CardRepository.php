<?php

namespace Infrastructure\Repositories\Financial\AccountsAndCards\Card;

use Domain\Financial\AccountsAndCards\Cards\DataTransferObjects\CardData;
use Domain\Financial\AccountsAndCards\Cards\Interfaces\CardRepositoryInterface;
use Domain\Financial\AccountsAndCards\Cards\Models\Card;
use Domain\Financial\Movements\Models\Movement;
use Illuminate\Support\Collection;
use Infrastructure\Repositories\BaseRepository;

class CardRepository extends BaseRepository implements CardRepositoryInterface
{

    protected mixed $model = Card::class;

    const TABLE_NAME = 'cards';
    const ID_COLUMN = 'cards.id';
    const CARD_NUMBER_COLUMN = 'card_number';
    const CARD_HOLDER_COLUMN = 'card_holder';
    const BALANCE_COLUMN = 'balance';
    const CARD_TYPE_COLUMN = 'card_type';
    const CARD_EXPIRATION_DATE_COLUMN = 'card_expiration_date';
    const CARD_IS_ACTIVE_COLUMN = 'card_is_active';
    const CARD_IS_DEFAULT_COLUMN = 'card_is_default';
    const CARD_IS_DELETED_COLUMN = 'card_is_deleted';

    const DISPLAY_SELECT_COLUMNS = [
        'cards.'
    ];


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
            'cardNumber' => $cardData->cardNumber,
            'expiryDate' => $cardData->expiryDate,
            'closingDate' => $cardData->closingDate,
            'status' => $cardData->status,
            'active' => $cardData->active,
            'creditCardBrand' => $cardData->creditCardBrand,
            'personType' => $cardData->personType,
            'cardHolderName' => $cardData->cardHolderName,
            'limit' => $cardData->limit,
        ]);
    }

    /**
     * Get all cards from the database.
     *
     * @return Collection
     */
    public function getCards(): Collection
    {
        //TODO: Implements here
    }

    /**
     * Get a specific card by ID.
     *
     * @param int $id
     * @return object|null
     */
    public function getCardById($id)
    {
        //TODO: Implements here
    }

    /**
     * Delete a card by ID.
     *
     * @param int $id
     * @return bool
     */
    public function deleteCard($id)
    {
        //TODO: Implements here
    }
}
