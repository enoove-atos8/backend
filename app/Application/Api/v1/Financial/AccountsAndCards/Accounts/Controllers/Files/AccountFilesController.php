<?php

namespace Application\Api\v1\Financial\AccountsAndCards\Accounts\Controllers\Files;

use Application\Api\v1\Financial\AccountsAndCards\Accounts\Requests\files\AccountFileRequest;
use Application\Api\v1\Financial\AccountsAndCards\Accounts\Resources\files\AccountsFilesResourcesCollection;
use Domain\Financial\AccountsAndCards\Accounts\Actions\files\GetAccountFilesAction;
use Domain\Financial\AccountsAndCards\Accounts\Actions\files\SaveAccountFileAction;
use Domain\Financial\AccountsAndCards\Accounts\Actions\SaveAccountAction;
use Domain\Financial\AccountsAndCards\Accounts\Constants\ReturnMessages;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\ResponseFactory;
use Symfony\Component\Console\Application;
use Symfony\Component\HttpFoundation\Response;
use Infrastructure\Exceptions\GeneralExceptions;

class AccountFilesController
{
    /**
     * Save a new account or update an existing one
     *
     * @param AccountFileRequest $accountFileRequest
     * @param SaveAccountFileAction $saveAccountFileAction
     * @return ResponseFactory|Application|Response
     * @throws GeneralExceptions
     */
    public function saveFile(AccountFileRequest $accountFileRequest, SaveAccountFileAction $saveAccountFileAction): ResponseFactory|Application|Response
    {
        try
        {
            $saveAccountFileAction->execute($accountFileRequest->accountFileData());

            return response([
                'message'   =>  ReturnMessages::ACCOUNT_CREATED,
            ], 201);

        } catch (Exception $e)
        {
            throw new GeneralExceptions($e->getMessage(), $e->getCode(), $e);
        }
    }


    /**
     * @param Request $request
     * @param GetAccountFilesAction $getAccountFilesAction
     * @return AccountsFilesResourcesCollection
     * @throws GeneralExceptions
     */
    public function getAccountsFiles(Request $request, GetAccountFilesAction $getAccountFilesAction): AccountsFilesResourcesCollection
    {
        try
        {
            $accountId = $request->input('accountId');
            $files = $getAccountFilesAction->execute($accountId);

            return new AccountsFilesResourcesCollection($files);
        }
        catch (Exception $e)
        {
            throw new GeneralExceptions($e->getMessage(), $e->getCode(), $e);
        }
    }
}
