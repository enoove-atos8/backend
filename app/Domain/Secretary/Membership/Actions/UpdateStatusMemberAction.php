<?php

namespace Domain\Secretary\Membership\Actions;

use App\Domain\SyncStorage\Constants\ReturnMessages;
use Domain\Secretary\Membership\Interfaces\MemberRepositoryInterface;
use Infrastructure\Exceptions\GeneralExceptions;

class UpdateStatusMemberAction
{
    public function __construct(
        private MemberRepositoryInterface $memberRepository,
        private SyncMemberCountAction $syncMemberCountAction
    ) {}

    /**
     * @throws GeneralExceptions
     */
    public function execute($memberId, $activated): bool
    {
        $updated = $this->memberRepository->updateStatus($memberId, $activated);

        if ($updated) {
            $this->syncMemberCountAction->execute();

            return true;
        }

        throw new GeneralExceptions(ReturnMessages::ERROR_UPDATE_STATUS_MEMBER, 500);
    }
}
