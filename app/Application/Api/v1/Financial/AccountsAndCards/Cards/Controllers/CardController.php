<?php

namespace Application\Api\v1\Financial\AccountsAndCards\Cards\Controllers;

use App\Application\Api\v1\Financial\AccountsAndCards\Cards\Resources\CardResource;
use Application\Api\v1\Financial\AccountsAndCards\Cards\Requests\CardRequest;
use Application\Api\v1\Financial\AccountsAndCards\Cards\Resources\CardsResourceCollection;
use Application\Core\Http\Controllers\Controller;
use Domain\Financial\AccountsAndCards\Cards\Actions\DeactivateCardAction;
use Domain\Financial\AccountsAndCards\Cards\Actions\GetCardByIdAction;
use Domain\Financial\AccountsAndCards\Cards\Actions\GetCardsAction;
use Domain\Financial\AccountsAndCards\Cards\Actions\SaveCardAction;
use Domain\Financial\AccountsAndCards\Cards\Constants\ReturnMessages;
use Domain\Financial\Exits\Purchases\Actions\GetInvoiceByIdAction;
use Exception;
use Illuminate\Console\Application;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Illuminate\Http\JsonResponse;

class CardController extends Controller
{
    /**
     * Save a new card or update an existing one
     *
     * @param CardRequest $request
     * @param SaveCardAction $saveCardAction
     * @return ResponseFactory|Application|Response
     * @throws GeneralExceptions
     */
    public function saveCard(CardRequest $request, SaveCardAction $saveCardAction): ResponseFactory|Application|Response
    {
        try
        {
            $saveCardAction->execute($request->cardData());

            return response([
                'message'   =>  ReturnMessages::CARD_CREATED,
            ], 201);

        } catch (Exception $e)
        {
            throw new GeneralExceptions($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @throws GeneralExceptions
     */
    public function getInvoicesByCardId(GetInvoiceByIdAction $getCardsAction): CardsResourceCollection
    {
        try
        {
            $cards = $getCardsAction->execute();

            return new CardsResourceCollection($cards);
        }
        catch (Exception $e)
        {
            throw new GeneralExceptions($e->getMessage(), $e->getCode(), $e);
        }
    }


    /**
     * @param Request $request
     * @param GetCardByIdAction $getCardByIdAction
     * @return CardResource
     * @throws GeneralExceptions
     */
    public function getCardById(Request $request, GetCardByIdAction $getCardByIdAction): CardResource
    {
        try
        {
            $id = $request->input('id');
            $card = $getCardByIdAction->execute($id);

            return new CardResource($card);
        }
        catch (Exception $e)
        {
            throw new GeneralExceptions($e->getMessage(), $e->getCode(), $e);
        }
    }


    /**
     * @param GetCardsAction $getCardsAction
     * @return CardsResourceCollection
     * @throws GeneralExceptions
     */
    public function getCards(GetCardsAction $getCardsAction): CardsResourceCollection
    {
        try
        {
            $cards = $getCardsAction->execute();

            return new CardsResourceCollection($cards);
        }
        catch (Exception $e)
        {
            throw new GeneralExceptions($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @throws GeneralExceptions
     */
    public function deactivateCard(Request $request, DeactivateCardAction $deactivateCardAction): Response|ResponseFactory
    {
        try
        {
            $id = $request->input('cardId');
            $deactivateCardAction->execute($id);

            return response([
                'message'   =>  ReturnMessages::CARD_DEACTIVATE,
            ], 200);

        } catch (Exception $e)
        {
            throw new GeneralExceptions($e->getMessage(), $e->getCode(), $e);
        }
    }
}
