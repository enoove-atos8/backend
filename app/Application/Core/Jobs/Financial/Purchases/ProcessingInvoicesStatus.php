<?php

namespace Application\Core\Jobs\Financial\Purchases;

use App\Domain\Financial\Exits\Purchases\Interfaces\CardInvoiceRepositoryInterface;
use Carbon\Carbon;
use Domain\CentralDomain\Churches\Church\Actions\GetChurchesAction;
use Domain\CentralDomain\Churches\Church\Constants\ReturnMessages;
use Domain\Financial\AccountsAndCards\Cards\Actions\GetCardsAction;
use Domain\Financial\Exits\Purchases\Actions\GetInvoicesByCardIdAction;
use Domain\Financial\Exits\Purchases\Actions\GetInvoicesByCardIdAndStatusAction;
use Domain\Financial\Exits\Purchases\Actions\UpdateStatusInstallmentAction;
use Domain\Financial\Exits\Purchases\Actions\UpdateStatusInvoiceAction;
use Domain\SyncStorage\Actions\UpdateStatusAction;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Financial\AccountsAndCards\Card\CardInvoiceRepository;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;
use Throwable;

class ProcessingInvoicesStatus
{
    private UpdateStatusInvoiceAction $updateStatusInvoiceAction;
    private GetInvoicesByCardIdAndStatusAction $getInvoicesByCardIdAndStatusAction;
    private GetCardsAction $getCardsAction;
    private GetChurchesAction $getChurchesAction;

    public function __construct(
        UpdateStatusInvoiceAction $updateStatusInvoiceAction,
        GetInvoicesByCardIdAndStatusAction $getInvoicesByCardIdAndStatusAction,
        GetCardsAction $getCardsAction,
        GetChurchesAction $getChurchesAction
    )
    {
        $this->updateStatusInvoiceAction = $updateStatusInvoiceAction;
        $this->getInvoicesByCardIdAndStatusAction = $getInvoicesByCardIdAndStatusAction;
        $this->getCardsAction = $getCardsAction;
        $this->getChurchesAction = $getChurchesAction;
    }


    /**
     * @throws GeneralExceptions
     * @throws TenantCouldNotBeIdentifiedById|Throwable
     */
    public function handle(): void
    {
        $tenants = $this->getActiveTenants();

        foreach ($tenants as $tenant)
        {
            tenancy()->initialize($tenant);

            $cards = $this->getCardsAction->execute();

            foreach ($cards as $card)
            {
                $openInvoices = $this->getInvoicesByCardIdAndStatusAction->execute($card->id, CardInvoiceRepository::INVOICE_OPEN_VALUE);
                $closedInvoices = $this->getInvoicesByCardIdAndStatusAction->execute($card->id, CardInvoiceRepository::INVOICE_CLOSED_VALUE);
                $currentDate = Carbon::now();

                foreach ($openInvoices as $invoice)
                {
                    $referenceYearMonth = Carbon::parse($invoice->referenceDate)->format('Y-m');
                    $referenceDate = Carbon::createFromFormat('Y-m-d', $referenceYearMonth.'-'.$card->closingDay);

                    $nextMonthYearMonth = Carbon::parse($currentDate)->addMonth()->format('Y-m');
                    $closingDateNextMonth = Carbon::createFromFormat('Y-m-d', $nextMonthYearMonth.'-'.$card->closingDay);

                    if ($currentDate->greaterThan($referenceDate) && $currentDate->lessThan($closingDateNextMonth))
                    {
                        $this->updateStatusInvoiceAction->execute($invoice->id, CardInvoiceRepository::INVOICE_CLOSED_VALUE);
                    }
                }

                foreach ($closedInvoices as $invoice)
                {
                    $referenceYearMonth = Carbon::parse($invoice->referenceDate)->format('Y-m');
                    $referenceDate = Carbon::createFromFormat('Y-m-d', $referenceYearMonth.'-'.$card->dueDay);

                    if ($currentDate->greaterThan($referenceDate))
                        $this->updateStatusInvoiceAction->execute($invoice->id, CardInvoiceRepository::INVOICE_LATE_VALUE);
                }
            }
        }
    }



    /**
     * @throws Throwable
     */
    public function getActiveTenants(): array
    {
        $arrTenants = [];
        $tenants = $this->getChurchesAction->execute();

        if(count($tenants) > 0)
        {
            foreach ($tenants as $tenant)
                $arrTenants[] = $tenant->tenant_id;
        }

        return $arrTenants;
    }
}
