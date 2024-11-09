<?php

namespace Domain\Financial\Receipts\Entries\ReadingError\Actions;

use Domain\Financial\Receipts\Entries\ReadingError\Interfaces\ReadingErrorReceiptRepositoryInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\Financial\Receipts\ReadingErrorReceiptRepository;

class GetReadingErrorReceiptsAction
{
    private ReadingErrorReceiptRepository $readingErrorReceiptRepository;

    public function __construct(
        ReadingErrorReceiptRepositoryInterface $readingErrorReceiptRepositoryInterface
    )
    {
        $this->readingErrorReceiptRepository = $readingErrorReceiptRepositoryInterface;
    }


    /**
     *
     * @param string $reason
     * @return Collection|null
     * @throws GeneralExceptions
     */
    public function __invoke(string $reason): Collection | null
    {
        $receipts = $this->readingErrorReceiptRepository->getReadingErrorReceipts($reason);

        if(count($receipts) > 0)
        {
            return $receipts;
        }
        else
        {
            throw new GeneralExceptions('Não existem comprovantes por aqui...', 404);
        }
    }
}
