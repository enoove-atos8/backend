<?php

namespace Application\Api\v1\Financial\Exits\Payments\Controllers;

use App\Domain\Financial\Exits\Payments\Items\Constants\ReturnMessages;
use Application\Api\v1\Financial\Exits\Payments\Requests\AddPaymentItemsRequest;
use Application\Api\v1\Financial\Exits\Payments\Resources\PaymentCategoriesMobileResourceCollection;
use Application\Api\v1\Financial\Exits\Payments\Resources\PaymentItemsMobileResourceCollection;
use Application\Api\v1\Financial\Exits\Payments\Resources\PaymentItemsResourceCollection;
use Application\Api\v1\Financial\Exits\Payments\Resources\PaymentsResourceCollection;
use Application\Core\Http\Controllers\Controller;
use Domain\Financial\Exits\Payments\Categories\Actions\GetPaymentsAction;
use Domain\Financial\Exits\Payments\Items\Actions\AddPaymentItemAction;
use Domain\Financial\Exits\Payments\Items\Actions\DeletePaymentItemAction;
use Domain\Financial\Exits\Payments\Items\Actions\GetPaymentItemsAction;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Infrastructure\Exceptions\GeneralExceptions;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Throwable;

class PaymentsController extends Controller
{
    /**
     * @param Request $request
     * @param GetPaymentsAction $getPaymentsAction
     * @return PaymentsResourceCollection|PaymentCategoriesMobileResourceCollection
     * @throws BindingResolutionException
     * @throws GeneralExceptions
     */
    public function getPaymentsCategories(Request $request, GetPaymentsAction $getPaymentsAction): PaymentsResourceCollection | PaymentCategoriesMobileResourceCollection
    {
        try
        {
            $source = $request->input('source');
            $payments = $getPaymentsAction->execute();

            if($source == 'web')
                return new PaymentsResourceCollection($payments);
            else if($source == 'app')
                return new PaymentCategoriesMobileResourceCollection($payments);

        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }


    /**
     * @param int $id
     * @param Request $request
     * @param GetPaymentItemsAction $getPaymentItemsAction
     * @return PaymentItemsResourceCollection
     * @throws BindingResolutionException
     * @throws GeneralExceptions
     * @throws Throwable
     */
    public function getPaymentItems(int $id, Request $request, GetPaymentItemsAction $getPaymentItemsAction): PaymentItemsResourceCollection | PaymentItemsMobileResourceCollection
    {
        try
        {
            $source = $request->input('source');
            $items = $getPaymentItemsAction->execute($id);

            if($source == 'web')
                return new PaymentItemsResourceCollection($items);
            else if($source == 'app')
                return new PaymentItemsMobileResourceCollection($items);

        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }


    /**
     * @param int $id
     * @param DeletePaymentItemAction $deletePaymentItemAction
     * @return ResponseFactory|Application|Response
     * @throws GeneralExceptions
     * @throws Throwable
     */
    public function deletePaymentItems(int $id, DeletePaymentItemAction $deletePaymentItemAction): ResponseFactory|Application|Response
    {
        try
        {
            $deleted = $deletePaymentItemAction->execute($id);

            if($deleted)
            {
                return response([
                    'message'   =>  ReturnMessages::DELETED_PAYMENTS_ITEMS_SUCCESS,
                ], 200);
            }
            else
            {
                return response([
                    'message'   =>  ReturnMessages::DELETED_PAYMENTS_ITEMS_ERROR,
                ], 500);
            }


        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }


    /**
     * @param AddPaymentItemsRequest $request
     * @param AddPaymentItemAction $addPaymentItemAction
     * @return ResponseFactory|Application|Response
     * @throws GeneralExceptions
     * @throws UnknownProperties
     */
    public function addPaymentItems(AddPaymentItemsRequest $request, AddPaymentItemAction $addPaymentItemAction): ResponseFactory|Application|Response
    {
        try
        {
            $addPaymentItemAction->execute($request->paymentItemData());

            return response([
                'message'   =>  ReturnMessages::ITEM_PAYMENTS_ITEMS_SUCCESS,
            ], 201);


        }
        catch (GeneralExceptions $e)
        {
            throw new GeneralExceptions($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
