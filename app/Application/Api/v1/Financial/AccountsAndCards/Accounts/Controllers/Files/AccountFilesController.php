<?php

namespace Application\Api\v1\Financial\AccountsAndCards\Accounts\Controllers\Files;

use App\Application\Api\v1\Financial\AccountsAndCards\Accounts\Requests\Files\AccountFileRequest;
use App\Application\Api\v1\Financial\AccountsAndCards\Accounts\Resources\Files\AccountsFilesResourcesCollection;
use App\Domain\Financial\AccountsAndCards\Accounts\Constants\Files\ReturnMessages;
use Domain\Financial\AccountsAndCards\Accounts\Actions\Files\ChangeFileProcessingStatusAction;
use Domain\Financial\AccountsAndCards\Accounts\Actions\Files\GetAccountFilesAction;
use Domain\Financial\AccountsAndCards\Accounts\Actions\Files\HandleFileProcessAction;
use Domain\Financial\AccountsAndCards\Accounts\Actions\Files\SaveAccountFileAction;
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
                'message'   =>  ReturnMessages::FILE_CREATED,
            ], 201);

        } catch (Exception $e)
        {
            throw new GeneralExceptions($e->getMessage(), $e->getCode(), $e);
        }
    }


    /**
     * Process a file as data extraction or bank conciliation
     *
     * @param Request $request
     * @param HandleFileProcessAction $handleFileProcessAction
     * @param ChangeFileProcessingStatusAction $changeFileProcessingStatusAction
     * @return ResponseFactory|Application|Response
     * @throws GeneralExceptions
     */
    public function processFile(Request $request, HandleFileProcessAction $handleFileProcessAction, ChangeFileProcessingStatusAction $changeFileProcessingStatusAction): ResponseFactory|Application|Response
    {
        try
        {
            $fileId = $request->input('id');
            $type = $request->input('type');
            $tenant = explode('.', $request->getHost())[0];

            $handleFileProcessAction->execute($fileId, $type, $tenant);

            return response([
                'message'   =>  ReturnMessages::FILE_PUT_TO_PROCESS,
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
