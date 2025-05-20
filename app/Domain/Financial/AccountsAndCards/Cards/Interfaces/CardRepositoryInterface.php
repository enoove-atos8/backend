<?php

namespace Domain\Financial\AccountsAndCards\Cards\Interfaces;

use Domain\Financial\AccountsAndCards\Cards\DataTransferObjects\CardData;
use Domain\Financial\AccountsAndCards\Cards\Models\Card;
use Illuminate\Support\Collection;

interface CardRepositoryInterface
{
    /**
     * Get all cards from the database.
     *
     * @return Collection
     */
    public function getCards(): Collection;

    /**
     * Get a specific card by ID.
     *
     * @param int $id
     * @return CardData|null
     */
    public function getCardById(int $id): ?CardData;

    /**
     * Delete a card by ID.
     *
     * @param int $id
     * @return bool
     */
    public function deleteCard(int $id): bool;

    /**
     * Save a new card or update an existing one.
     *
     * @param CardData $cardData
     * @return Card
     */
    public function saveCard(CardData $cardData): Card;
}
