<?php

namespace Application\Api\v1\Billing\Controllers;

use App\Domain\CentralDomain\Billing\Actions\AddPaymentMethodAction;
use App\Domain\CentralDomain\Billing\Actions\DeletePaymentMethodAction;
use App\Domain\CentralDomain\Billing\Actions\ListPaymentMethodsAction;
use App\Domain\CentralDomain\Billing\Actions\SetDefaultPaymentMethodAction;
use App\Domain\CentralDomain\Billing\Constants\SubscriptionMessages;
use Application\Core\Http\Controllers\Controller;
use Domain\CentralDomain\Churches\Church\Actions\GetChurchAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Infrastructure\Exceptions\GeneralExceptions;

class PaymentMethodController extends Controller
{
    public function __construct(
        private GetChurchAction $getChurchAction,
        private ListPaymentMethodsAction $listPaymentMethodsAction,
        private AddPaymentMethodAction $addPaymentMethodAction,
        private SetDefaultPaymentMethodAction $setDefaultPaymentMethodAction,
        private DeletePaymentMethodAction $deletePaymentMethodAction
    ) {}

    public function getPaymentMethods(Request $request): JsonResponse
    {
        $church = $this->getChurchFromRequest($request);

        if (! $church->stripeId) {
            return response()->json(['data' => []]);
        }

        $paymentMethods = $this->listPaymentMethodsAction->execute($church->stripeId);

        return response()->json(['data' => $paymentMethods]);
    }

    public function addPaymentMethod(Request $request): JsonResponse
    {
        $request->validate([
            'payment_method_id' => 'required|string',
            'set_as_default' => 'nullable|boolean',
        ]);

        $church = $this->getChurchFromRequest($request);

        if (! $church->stripeId) {
            throw new GeneralExceptions('Igreja não possui customer no Stripe', 400);
        }

        $paymentMethod = $this->addPaymentMethodAction->execute(
            $church->stripeId,
            $request->input('payment_method_id'),
            $request->input('set_as_default', false)
        );

        return response()->json([
            'message' => SubscriptionMessages::SUCCESS_PAYMENT_METHOD_ADDED,
            'data' => $paymentMethod,
        ], 201);
    }

    public function setDefaultPaymentMethod(Request $request, string $paymentMethodId): JsonResponse
    {
        $church = $this->getChurchFromRequest($request);

        if (! $church->stripeId) {
            throw new GeneralExceptions('Igreja não possui customer no Stripe', 400);
        }

        $this->setDefaultPaymentMethodAction->execute($church->stripeId, $paymentMethodId);

        return response()->json([
            'message' => SubscriptionMessages::SUCCESS_DEFAULT_PAYMENT_METHOD_SET,
        ]);
    }

    public function deletePaymentMethod(Request $request, string $paymentMethodId): JsonResponse
    {
        $church = $this->getChurchFromRequest($request);

        if (! $church->stripeId) {
            throw new GeneralExceptions('Igreja não possui customer no Stripe', 400);
        }

        $this->deletePaymentMethodAction->execute($church->stripeId, $paymentMethodId);

        return response()->json([
            'message' => SubscriptionMessages::SUCCESS_PAYMENT_METHOD_DELETED,
        ]);
    }

    private function getChurchFromRequest(Request $request)
    {
        $tenant = explode('.', $request->getHost())[0];
        $church = $this->getChurchAction->execute($tenant);

        if (! $church) {
            throw new GeneralExceptions('Igreja não encontrada', 404);
        }

        return $church;
    }
}
