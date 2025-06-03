<?php

namespace Domain\Financial\Exits\Purchases\Actions;

use App\Domain\Financial\Exits\Purchases\DataTransferObjects\CardInvoiceData;
use App\Domain\Financial\Exits\Purchases\Interfaces\CardInvoiceRepositoryInterface;
use DateTime;
use Domain\Financial\AccountsAndCards\Cards\Constants\ReturnMessages;
use Exception;
use Infrastructure\Exceptions\GeneralExceptions;

class CreateInvoiceAction
{
    private CardInvoiceRepositoryInterface $cardInvoiceRepository;


    public function __construct(CardInvoiceRepositoryInterface $cardInvoiceRepository)
    {
        $this->cardInvoiceRepository = $cardInvoiceRepository;
    }


    /**
     * @param CardInvoiceData $cardInvoiceData
     * @param string $closingDay
     * @param string $purchaseCreditCardDate
     * @return CardInvoiceData|null
     * @throws GeneralExceptions
     * @throws Exception
     */
    public function execute(CardInvoiceData $cardInvoiceData, string $closingDay, string $purchaseCreditCardDate): ?CardInvoiceData
    {

        $referenceDate = self::calculateReferenceDate($closingDay, $purchaseCreditCardDate);
        $cardInvoiceData->referenceDate = $referenceDate;

        $invoice = $this->cardInvoiceRepository->createInvoice($cardInvoiceData);

        if(!is_null($invoice->id))
            return $invoice;

        else
            throw new GeneralExceptions(ReturnMessages::CARDS_NOT_CREATED, 500);
    }


    /**
     * @throws Exception
     */
    public static function calculateReferenceDate(string $closingDay, string $purchaseCreditCardDate): string
    {
        $purchaseDate = new DateTime($purchaseCreditCardDate);
        $purchaseDay = (int) $purchaseDate->format('d');

        if ($purchaseDay >= (int) $closingDay)
            $referenceDate = $purchaseDate->modify('+1 month')->format('Y-m');
        else
            $referenceDate = $purchaseDate->format('Y-m');

        return $referenceDate;
    }

}
