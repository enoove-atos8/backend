<?php

namespace Domain\Financial\Exits\Purchases\Actions;

use App\Domain\Financial\Exits\Purchases\DataTransferObjects\CardInstallmentData;
use App\Domain\Financial\Exits\Purchases\DataTransferObjects\CardPurchaseData;
use App\Domain\Financial\Exits\Purchases\Interfaces\CardPurchaseRepositoryInterface;
use Domain\Financial\Exits\Purchases\Constants\ReturnMessages;
use Infrastructure\Exceptions\GeneralExceptions;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class CreatePurchaseAction
{
    private CardPurchaseRepositoryInterface $cardPurchaseRepository;
    private CreateInstallmentAction $createInstallmentAction;


    public function __construct(
        CardPurchaseRepositoryInterface $cardPurchaseRepository,
        CreateInstallmentAction $createInstallmentAction)
    {
        $this->cardPurchaseRepository = $cardPurchaseRepository;
        $this->createInstallmentAction = $createInstallmentAction;
    }


    /**
     * @param CardPurchaseData $cardPurchaseData
     * @return CardPurchaseData|null
     * @throws GeneralExceptions
     * @throws UnknownProperties
     */
    public function execute(CardPurchaseData $cardPurchaseData): ?CardPurchaseData
    {
        $purchase = $this->cardPurchaseRepository->createPurchase($cardPurchaseData);
        $installments = $purchase->installments;
        $previousReference = null;

        for($installment = 1; $installment <= $installments; $installment++)
        {
            $isFirstInstallment = $installment == 1;

            $installmentData = CardInstallmentData::fromPurchaseData(
                $cardPurchaseData,
                $purchase->id,
                $installment,
                $isFirstInstallment);

            $installmentCreated = $this->createInstallmentAction->execute($installmentData, $previousReference);
            $previousReference = $installmentCreated->date;
        }

        if(!is_null($purchase->id))
            return $purchase;

        else
            throw new GeneralExceptions(ReturnMessages::PURCHASE_NOT_CREATED, 500);
    }
}
