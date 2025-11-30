<?php

namespace App\Application\Api\v1\Financial\AccountsAndCards\Accounts\Controllers;

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
use Infrastructure\Exceptions\GeneralExceptions;
use Symfony\Component\Console\Application;
use Symfony\Component\HttpFoundation\Response;

class AccountFilesController
{
    /**
     * Save a new account or update an existing one
     *
     * @throws GeneralExceptions
     */
    public function saveFile(AccountFileRequest $accountFileRequest, SaveAccountFileAction $saveAccountFileAction): ResponseFactory|Application|Response
    {
        try {
            $saveAccountFileAction->execute($accountFileRequest->accountFileData());

            return response([
                'message' => ReturnMessages::FILE_CREATED,
            ], 201);

        } catch (Exception $e) {
            throw new GeneralExceptions($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Process a file as data extraction or bank conciliation
     *
     * @throws GeneralExceptions
     */
    public function processFile(Request $request, HandleFileProcessAction $handleFileProcessAction, ChangeFileProcessingStatusAction $changeFileProcessingStatusAction): ResponseFactory|Application|Response
    {
        try {
            $fileId = $request->input('id');
            $type = $request->input('type');
            $forceProcess = $request->boolean('forceProcess', false);
            $initialBalance = $request->input('initialBalance');
            $initialBalanceDate = $request->input('initialBalanceDate');
            $tenant = explode('.', $request->getHost())[0];

            $result = $handleFileProcessAction->execute(
                $fileId,
                $type,
                $tenant,
                $forceProcess,
                $initialBalance !== null ? (float) $initialBalance : null,
                $initialBalanceDate
            );

            // Se requer confirmação para meses futuros, retornar 409
            if ($result !== null && isset($result['requiresConfirmation']) && $result['requiresConfirmation']) {
                return response([
                    'message' => sprintf(ReturnMessages::FUTURE_MONTHS_EXIST, $result['futureMonths'], $result['currentMonth']),
                    'requiresConfirmation' => true,
                    'futureMonths' => $result['futureMonths'],
                ], 409);
            }

            // Se requer saldo inicial, retornar 422
            if ($result !== null && isset($result['requiresInitialBalance']) && $result['requiresInitialBalance']) {
                return response([
                    'message' => sprintf(
                        ReturnMessages::INITIAL_BALANCE_REQUIRED,
                        $result['requiredBalanceMonthFormatted'],
                        $result['currentMonth']
                    ),
                    'requiresInitialBalance' => true,
                    'requiredBalanceMonth' => $result['requiredBalanceMonth'],
                    'requiredBalanceMonthFormatted' => $result['requiredBalanceMonthFormatted'],
                    'currentBalanceMonth' => $result['currentBalanceMonth'],
                    'currentBalanceMonthFormatted' => $result['currentBalanceMonthFormatted'],
                    'currentBalance' => $result['currentBalance'],
                ], 422);
            }

            return response([
                'message' => ReturnMessages::FILE_PUT_TO_PROCESS,
            ], 201);

        } catch (Exception $e) {
            throw new GeneralExceptions($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @throws GeneralExceptions
     */
    public function getAccountsFiles(Request $request, GetAccountFilesAction $getAccountFilesAction): AccountsFilesResourcesCollection
    {
        try {
            $accountId = $request->input('accountId');
            $files = $getAccountFilesAction->execute($accountId);

            return new AccountsFilesResourcesCollection($files);
        } catch (Exception $e) {
            throw new GeneralExceptions($e->getMessage(), is_int($e->getCode()) ? $e->getCode() : 500, $e);
        }
    }
}
