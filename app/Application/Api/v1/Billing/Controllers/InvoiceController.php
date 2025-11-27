<?php

namespace Application\Api\v1\Billing\Controllers;

use App\Domain\CentralDomain\Billing\Actions\ListInvoicesAction;
use App\Domain\CentralDomain\Billing\Actions\PayInvoiceAction;
use App\Domain\CentralDomain\Billing\Constants\InvoiceMessages;
use App\Domain\CentralDomain\Billing\Constants\PaymentMethodType;
use Application\Api\v1\Billing\Requests\PayInvoiceRequest;
use Application\Api\v1\Billing\Resources\InvoiceResource;
use Application\Core\Http\Controllers\Controller;
use Domain\CentralDomain\Churches\Church\Actions\GetChurchAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Infrastructure\Exceptions\GeneralExceptions;

class InvoiceController extends Controller
{
    public function __construct(
        private GetChurchAction $getChurchAction,
        private ListInvoicesAction $listInvoicesAction,
        private PayInvoiceAction $payInvoiceAction
    ) {}

    /**
     * List all invoices for the authenticated church
     */
    public function getInvoices(Request $request): AnonymousResourceCollection
    {
        $church = $this->getChurchFromRequest($request);

        if (! $church->stripeId) {
            return InvoiceResource::collection(collect([]));
        }

        $invoices = $this->listInvoicesAction->execute($church->stripeId);

        return InvoiceResource::collection($invoices);
    }

    /**
     * Pay an invoice with Boleto or PIX
     */
    public function payInvoice(PayInvoiceRequest $request, string $invoiceId): JsonResponse
    {
        $church = $this->getChurchFromRequest($request);

        if (! $church->stripeId) {
            throw new GeneralExceptions(InvoiceMessages::ERROR_NO_STRIPE_CUSTOMER, 400);
        }

        $payInvoiceData = $request->setPayInvoiceData();

        $result = $this->payInvoiceAction->execute(
            $church->stripeId,
            $invoiceId,
            $payInvoiceData
        );

        $responseKey = $payInvoiceData->paymentMethod === PaymentMethodType::BOLETO
            ? PaymentMethodType::BOLETO
            : PaymentMethodType::PIX;

        return response()->json([
            'data' => [
                $responseKey => $result,
            ],
        ]);
    }

    /**
     * Get church from request
     *
     * @throws GeneralExceptions
     */
    private function getChurchFromRequest(Request $request)
    {
        $tenant = explode('.', $request->getHost())[0];
        $church = $this->getChurchAction->execute($tenant);

        if (! $church) {
            throw new GeneralExceptions(InvoiceMessages::ERROR_CHURCH_NOT_FOUND, 404);
        }

        return $church;
    }
}
