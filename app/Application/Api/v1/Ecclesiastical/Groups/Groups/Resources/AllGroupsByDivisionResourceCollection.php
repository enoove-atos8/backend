<?php

namespace App\Application\Api\v1\Ecclesiastical\Groups\Groups\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class AllGroupsByDivisionResourceCollection extends JsonResource
{
    /**
     * Disable data wrapping
     *
     * @var string|null
     */
    public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        $result = ['divisions' => []];

        foreach ($this->resource as $item) {
            $division = $item['division'];
            $groups = $item['groups'];

            $result['divisions'][] = [
                'divisionId' => $division->id,
                'divisionName' => $division->name,
                'divisionSlug' => $division->slug,
                'requireLeader' => $division->requireLeader,
                'groups' => [
                    'data' => $this->formatGroups($groups),
                ],
            ];
        }

        return $result;
    }

    /**
     * Formata os grupos para o formato de resposta da API
     */
    private function formatGroups($groups): array
    {
        $result = [];

        foreach ($groups as $group) {
            $result[] = [
                'id' => $group->id ?? null,
                'name' => $group->name ?? null,
                'slug' => $group->slug ?? null,
                'enabled' => $group->enabled ?? null,
                'transactionsExists' => $group->financialMovement ?? null,
                'leader' => $this->getLeaderData($group),
            ];
        }

        return $result;
    }

    /**
     * Obtém os dados do líder do grupo se disponível
     */
    private function getLeaderData($item): ?array
    {
        if (isset($item->leader) && $item->leader->id) {
            return [
                'id' => $item->leader->id,
                'fullName' => $item->leader->fullName,
                'avatar' => $item->leader->avatar,
                'cellPhone' => $item->leader->cellPhone,
                'email' => $item->leader->email,
                'titheHistory' => $item->leader->titheHistory ?? [],
            ];
        }

        return null;
    }
}
