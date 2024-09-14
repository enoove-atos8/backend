<?php

namespace Domain\Members\Interfaces;

use Domain\Members\DataTransferObjects\MemberData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Domain\Members\Models\Member;

interface MemberRepositoryInterface
{
    public function createMember(MemberData $memberData): Member;

    public function getMembers(): Member|Collection;
    public function getMembersByMiddleCpf(string $cpf): Model | null;
    public function getMembersByCpf(string $cpf, bool $middleCpf = false): Model | null;
    public function getMemberAsGroupLeader(int $groupId, bool $groupLeader = true): Member|Collection;
    public function updateStatus($id, $status): mixed;
    public function updateMiddleCpf(int $memberId, string $middleCpf): mixed;
    public function updateMember($id, MemberData $memberData): mixed;
}
