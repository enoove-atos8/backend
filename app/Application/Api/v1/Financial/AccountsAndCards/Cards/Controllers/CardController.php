<?php

namespace Application\Api\v1\Financial\AccountsAndCards\Cards\Controllers;

use Application\Api\v1\Financial\AccountsAndCards\Cards\Requests\CardRequest;
use Application\Api\v1\Financial\AccountsAndCards\Cards\Resources\CardsResourceCollection;
use Application\Core\Http\Controllers\Controller;
use Domain\Financial\AccountsAndCards\Cards\Actions\GetCardsAction;
use Domain\Financial\AccountsAndCards\Cards\Actions\SaveCardAction;
use Exception;
use Illuminate\Console\Application;
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
                'message'   =>  '',
            ], 201);

        } catch (Exception $e)
        {
            throw new GeneralExceptions($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
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

    public function getCardById()
    {
        //TODO: Implements here
    }

    public function deleteCard()
    {
        //TODO: Implements here
    }
}
