<?php

namespace Domain\Financial\Receipts\Entries\ReadingError\Actions;

use App\Infrastructure\Repositories\Financial\Entries\Automation\AutomationRepository;
use Domain\Financial\Receipts\Entries\ReadingError\Interfaces\ReadingErrorReceiptRepositoryInterface;
use Illuminate\Support\Collection;
use Infrastructure\Exceptions\GeneralExceptions;

class GetReadingErrorReceiptsAction
{
    private AutomationRepository $readingErrorReceiptRepository;

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
    public function execute(string $reason): Collection | null
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
