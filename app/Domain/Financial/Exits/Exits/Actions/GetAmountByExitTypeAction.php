<?php

namespace Domain\Financial\Exits\Exits\Actions;

use Domain\Financial\Exits\Exits\Interfaces\ExitRepositoryInterface;
use Infrastructure\Repositories\BaseRepository;
use Infrastructure\Repositories\Financial\Exits\Exits\ExitRepository;
use Throwable;

class GetAmountByExitTypeAction
{
    private ExitRepositoryInterface $exitRepository;

    public function __construct(ExitRepositoryInterface $exitRepositoryInterface)
    {
        $this->exitRepository = $exitRepositoryInterface;
    }



    /**
     * @throws Throwable
     */
    public function execute($rangeDates, $exitType = 'all'): null | array
    {
        $exits = $this->exitRepository->getAmountByExitType($rangeDates, $exitType);
        $totalPayment = $exits
            ->where(ExitRepository::EXIT_TYPE_COLUMN,
                BaseRepository::OPERATORS['EQUALS'],
                ExitRepository::PAYMENTS_VALUE)->sum(ExitRepository::AMOUNT_COLUMN);

        $totalTransfer = $exits
            ->where(ExitRepository::EXIT_TYPE_COLUMN,
                BaseRepository::OPERATORS['EQUALS'],
                ExitRepository::TRANSFER_VALUE)->sum(ExitRepository::AMOUNT_COLUMN);

        $totalMinisterialTransfers = $exits
            ->where(ExitRepository::EXIT_TYPE_COLUMN,
                BaseRepository::OPERATORS['EQUALS'],
                ExitRepository::MINISTERIAL_TRANSFER_VALUE)->sum(ExitRepository::AMOUNT_COLUMN);

        $totalContributions = $exits
            ->where(ExitRepository::EXIT_TYPE_COLUMN,
                BaseRepository::OPERATORS['EQUALS'],
                ExitRepository::CONTRIBUTIONS_VALUE)->sum(ExitRepository::AMOUNT_COLUMN);


        return [
            'payments'              =>  $totalPayment,
            'transfers'             =>  $totalTransfer,
            'ministerialTransfers'  =>  $totalMinisterialTransfers,
            'contributions'         =>  $totalContributions,
            'total'                 =>  $totalPayment + $totalTransfer + $totalMinisterialTransfers + $totalContributions,
        ];
    }
}
