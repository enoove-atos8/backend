<?php

namespace App\Domain\Financial\Reports\Purchases\Actions;

use App\Application\Core\Jobs\Financial\Reports\Purchases\HandlerPurchasesReports;
use App\Domain\Financial\Reports\Purchases\Constants\ReturnMessages;
use App\Domain\Financial\Reports\Purchases\DataTransferObjects\MonthlyPurchasesReportData;
use App\Domain\Financial\Reports\Purchases\Interfaces\MonthlyPurchasesReportsRepositoryInterface;
use Domain\Financial\AccountsAndCards\Cards\Actions\GetCardsAction;
use Infrastructure\Exceptions\GeneralExceptions;

class CreateMonthlyReceiptsPurchaseReportAction
{
    private MonthlyPurchasesReportsRepositoryInterface $monthlyPurchasesReportsRepository;

    private GetCardsAction $getCardsAction;

    public function __construct(
        MonthlyPurchasesReportsRepositoryInterface $monthlyPurchasesReportsRepositoryInterface,
        GetCardsAction $getCardsAction
    ) {
        $this->monthlyPurchasesReportsRepository = $monthlyPurchasesReportsRepositoryInterface;
        $this->getCardsAction = $getCardsAction;
    }


    /**
     * @throws GeneralExceptions
     */
    public function execute(MonthlyPurchasesReportData $monthlyReceiptsPurchaseReportData): void
    {
        $cardsByTenant = $this->getCardsAction->execute();

        if (count($cardsByTenant) > 0) {
            foreach ($cardsByTenant as $card) {
                $monthlyReceiptsPurchaseReportData->cardId = $card->id;
                $report = $this->monthlyPurchasesReportsRepository->generateMonthlyReceiptsPurchaseReport($monthlyReceiptsPurchaseReportData);

                if (!is_null($report->id)) {
                    HandlerPurchasesReports::dispatch();
                } else {
                    throw new GeneralExceptions(ReturnMessages::SUCCESS_REPORT_SEND_TO_PROCESS, 500);
                }
            }
        } else {
            throw new GeneralExceptions(ReturnMessages::ERROR_CARDS_NOT_FOUND, 404);
        }
    }
}
