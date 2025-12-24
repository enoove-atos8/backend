<?php

namespace Domain\Ecclesiastical\Groups\AmountRequests\Actions;

use Domain\Ecclesiastical\Groups\AmountRequests\Interfaces\AmountRequestRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;

class UpdateReceiptImageUrlAction
{
    private AmountRequestRepositoryInterface $repository;

    public function __construct(AmountRequestRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Update receipt image URL after file upload
     *
     * @throws GeneralExceptions
     */
    public function execute(int $receiptId, string $imageUrl): bool
    {
        $updated = $this->repository->updateReceiptImageUrl($receiptId, $imageUrl);

        if (! $updated) {
            throw new GeneralExceptions('Erro ao atualizar URL do comprovante!', 500);
        }

        return true;
    }
}
