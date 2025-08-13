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

    public function getMembers(array $filters, string | null $term, bool $paginate): array;
    public function getMembersByMiddleCpf(string $cpf): Model | null;
    public function getMembersByCpf(string $cpf): Model | null;

    public function getMemberById(string $id): MemberData | null;

    public function getMembersByBornMonth(string $month, string $fields): Collection | null;
    public function getTithersByMonth(string $month, bool $paginate): Collection | Paginator;

    public function getMemberAsGroupLeader(int $groupId, bool $groupLeader = true): Member|Collection;
    public function updateStatus($id, $status): mixed;
    public function updateMiddleCpf(int $memberId, string $middleCpf): mixed;
    public function updateMember($id, MemberData $memberData): mixed;
}
