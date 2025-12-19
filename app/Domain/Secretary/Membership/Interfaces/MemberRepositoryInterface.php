<?php

namespace Domain\Secretary\Membership\Interfaces;

use Domain\Secretary\Membership\DataTransferObjects\MemberData;
use Domain\Secretary\Membership\Models\Member;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

interface MemberRepositoryInterface
{
    public function createMember(MemberData $memberData): Member;

    public function getMembers(array $filters, ?string $term, bool $paginate): array;

    public function getMembersByMiddleCpf(string $cpf): ?Model;

    public function getMembersByCpf(string $cpf): ?Model;

    public function getMemberById(string $id): ?MemberData;

    public function getMembersByBornMonth(string $month, string $fields): ?Collection;

    public function getTithersByMonth(string $month, bool $paginate): Collection|Paginator;

    public function getMemberAsGroupLeader(int $groupId, bool $groupLeader = true): Member|Collection;

    public function getMembersByGroupId(int $groupId): ?Collection;

    public function addMembersToGroup(int $groupId, array $memberIds): bool;

    public function updateStatus($id, $status): mixed;

    public function updateMiddleCpf(int $memberId, string $middleCpf): mixed;

    public function updateMember($id, MemberData $memberData): mixed;

    public function getDependentsMembersIds(int $memberId): ?array;

    public function getPrincipalMemberId(int $memberId): ?int;

    /**
     * Create multiple members in bulk (single INSERT)
     *
     * @param  MemberData[]  $membersData
     */
    public function batchCreateMembers(array $membersData): bool;

    /**
     * Count active members (activated = true and deleted = false)
     */
    public function countActiveMembers(): int;

    /**
     * Update deactivation reason for a member
     */
    public function updateDeactivationReason(int $id, ?string $reason): bool;
}
