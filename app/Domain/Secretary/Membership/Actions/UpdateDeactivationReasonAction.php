<?php

namespace Domain\Secretary\Membership\Actions;

use Domain\Secretary\Membership\Constants\ReturnMessages;
use Domain\Secretary\Membership\Interfaces\MemberRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;

class UpdateDeactivationReasonAction
{
    public function __construct(
        private MemberRepositoryInterface $memberRepository
    ) {}

    /**
     * @throws GeneralExceptions
     */
    public function execute(int $memberId, ?string $reason): bool
    {
        $updated = $this->memberRepository->updateDeactivationReason($memberId, $reason);

        if ($updated) {
            return true;
        }

        throw new GeneralExceptions(ReturnMessages::ERROR_UPDATE_DEACTIVATION_REASON, 500);
    }
}
