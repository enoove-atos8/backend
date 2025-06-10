<?php

namespace Application\Core\Jobs\Financial\Purchases;

use App\Domain\Financial\Exits\Purchases\Interfaces\CardInvoiceRepositoryInterface;
use Carbon\Carbon;
use Domain\CentralDomain\Churches\Church\Actions\GetChurchesAction;
use Domain\CentralDomain\Churches\Church\Constants\ReturnMessages;
use Domain\Financial\AccountsAndCards\Cards\Actions\GetCardsAction;
use Domain\Financial\Exits\Purchases\Actions\GetInvoicesByCardIdAction;
use Domain\Financial\Exits\Purchases\Actions\GetInvoicesByCardIdAndStatusAction;
use Domain\Financial\Exits\Purchases\Actions\UpdateStatusInvoiceAction;
use Domain\SyncStorage\Actions\UpdateStatusAction;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Financial\AccountsAndCards\Card\CardInvoiceRepository;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;
use Throwable;

class ProcessingInvoicesClosing
{
    private UpdateStatusInvoiceAction $updateStatusInvoiceAction;
    private GetInvoicesByCardIdAndStatusAction $getInvoicesByCardIdAndStatusAction;
    private GetCardsAction $getCardsAction;
    private GetChurchesAction $getChurchesAction;

    public function __construct(
        UpdateStatusInvoiceAction $updateStatusInvoiceAction,
        GetInvoicesByCardIdAndStatusAction $getInvoicesByCardIdAndStatusAction,
        GetCardsAction $getCardsAction,
        GetChurchesAction $getChurchesAction,
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
                $invoices = $this->getInvoicesByCardIdAndStatusAction->execute($card->id, CardInvoiceRepository::INVOICE_OPEN_VALUE);

                foreach ($invoices as $invoice)
                {
                    // Get current date for comparison
                    $currentDate = Carbon::now();

                    // Format reference date by using the year-month from invoice and the closingDay from card
                    $referenceYearMonth = Carbon::parse($invoice->referenceDate)->format('Y-m');
                    $referenceDate = Carbon::createFromFormat('Y-m-d', $referenceYearMonth.'-'.$card->closingDay);

                    // Calculate next month's closing date
                    $nextMonthYearMonth = Carbon::parse($invoice->referenceDate)->addMonth()->format('Y-m');
                    $closingDateNextMonth = Carbon::createFromFormat('Y-m-d', $nextMonthYearMonth.'-'.$card->closingDay);

                    // If current date is greater than the reference closing date, close the invoice
                    if ($currentDate->greaterThan($referenceDate) && $currentDate->lessThan($closingDateNextMonth))
                        $this->updateStatusInvoiceAction->execute($invoice->id, CardInvoiceRepository::INVOICE_CLOSED_VALUE);
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

            return $arrTenants;
        }
        else
        {
            throw new GeneralExceptions(ReturnMessages::NOT_FOUND_CHURCHES, 404);
        }
    }
}
