<?php

namespace Domain\Members\Interfaces;

use Domain\Members\DataTransferObjects\MemberData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Domain\Members\Models\Member;

interface MemberRepositoryInterface
{
    public function createMember(MemberData $memberData): Member;

    public function getMembers(array $filters, string | null $term, bool $paginate): Collection | Paginator;
    public function getMembersByMiddleCpf(string $cpf): Model | null;
    public function getMembersByCpf(string $cpf): Model | null;
    public function getMemberAsGroupLeader(int $groupId, bool $groupLeader = true): Member|Collection;
    public function updateStatus($id, $status): mixed;
    public function updateMiddleCpf(int $memberId, string $middleCpf): mixed;
    public function updateMember($id, MemberData $memberData): mixed;
}
