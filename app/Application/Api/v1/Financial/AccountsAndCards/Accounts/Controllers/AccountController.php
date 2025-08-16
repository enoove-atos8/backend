<?php

namespace Application\Api\v1\Financial\AccountsAndCards\Accounts\Controllers;

use Application\Api\v1\Financial\AccountsAndCards\Accounts\Requests\AccountRequest;
use Application\Api\v1\Financial\AccountsAndCards\Accounts\Resources\AccountsResourcesCollection;
use Application\Core\Http\Controllers\Controller;
use Domain\Financial\AccountsAndCards\Accounts\Actions\DeactivateAccountAction;
use Domain\Financial\AccountsAndCards\Accounts\Actions\GetAccountsAction;
use Domain\Financial\AccountsAndCards\Accounts\Actions\SaveAccountAction;
use Domain\Financial\AccountsAndCards\Accounts\Constants\ReturnMessages;
use Exception;
use Illuminate\Routing\ResponseFactory;
use Infrastructure\Exceptions\GeneralExceptions;
use Symfony\Component\Console\Application;
use Symfony\Component\HttpFoundation\Response;

class AccountController extends Controller
{
    /**
     * Save a new account or update an existing one
     *
     * @param AccountRequest $accountRequest
     * @param SaveAccountAction $saveAccountAction
     * @return ResponseFactory|Application|Response
     * @throws GeneralExceptions
     */
    public function saveAccount(AccountRequest $accountRequest, SaveAccountAction $saveAccountAction): ResponseFactory|Application|Response
    {
        try
        {
            $saveAccountAction->execute($accountRequest->accountData());

            return response([
                'message'   =>  ReturnMessages::ACCOUNT_CREATED,
            ], 201);

        } catch (Exception $e)
        {
            throw new GeneralExceptions($e->getMessage(), $e->getCode(), $e);
        }
    }




    /**
     * Save a new account or update an existing one
     *
     * @param $id
     * @param DeactivateAccountAction $deactivateAccountAction
     * @return ResponseFactory|Application|Response
     * @throws GeneralExceptions
     */
    public function deactivateAccount($id, DeactivateAccountAction $deactivateAccountAction): ResponseFactory|Application|Response
    {
        try
        {
            $deactivateAccountAction->execute($id);

            return response([
                'message'   =>  ReturnMessages::ACCOUNT_DEACTIVATED,
            ], 200);

        } catch (Exception $e)
        {
            throw new GeneralExceptions($e->getMessage(), $e->getCode(), $e);
        }
    }




    /**
     * @param GetAccountsAction $getAccountsAction
     * @return AccountsResourcesCollection
     * @throws GeneralExceptions
     */
    public function getAccounts(GetAccountsAction $getAccountsAction): AccountsResourcesCollection
    {
        try
        {
            $cards = $getAccountsAction->execute();

            return new AccountsResourcesCollection($cards);
        }
        catch (Exception $e)
        {
            throw new GeneralExceptions($e->getMessage(), $e->getCode(), $e);
        }
    }
}
