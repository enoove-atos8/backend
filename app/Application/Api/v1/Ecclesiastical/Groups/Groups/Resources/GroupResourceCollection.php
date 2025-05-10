<?php

namespace App\Application\Api\v1\Ecclesiastical\Groups\Groups\Resources;

use Domain\Ecclesiastical\Divisions\DataTransferObjects\DivisionData;
use Domain\Ecclesiastical\Groups\DataTransferObjects\GroupData;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use JsonSerializable;

class GroupResourceCollection extends ResourceCollection
{
    /**
     * Replace the 'data' key in the JSON response
     * with the one declared in the 'wrap' variable
     * @var string
     */
    public static $wrap = 'groups';

    private ?DivisionData $division;

    /**
     * @param mixed $resource
     * @param DivisionData|null $division
     */
    public function __construct($resource, ?DivisionData $division = null)
    {
        parent::__construct($resource);
        $this->division = $division;
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        $result = ['data' => []];

        // Adiciona informações da divisão se disponível
        if ($this->division) {
            $result['divisionId'] = $this->division->id;
            $result['requireLeader'] = $this->division->requireLeader;
        }

        // Processa cada item da coleção
        foreach ($this->collection as $item) {
            $result['data'][] = $this->formatGroupItem($item);
        }

        return $result;
    }

    /**
     * Formata um item de grupo para o formato de resposta da API
     *
     * @param mixed $item
     * @return array
     */
    private function formatGroupItem($item): array
    {
        // Item básico com propriedades comuns
        return [
            'id' => $item->id ?? null,
            'name' => $item->name ?? null,
            'slug' => $item->slug ?? null,
            'enabled' => $item->enabled ?? null,
            'transactionsExists' => $item->financialMovement ?? null,
            'leader' => $this->getLeaderData($item),
        ];
    }

    /**
     * Obtém os dados do líder do grupo se disponível e necessário
     *
     * @param mixed $item
     * @return array|null
     */
    private function getLeaderData($item): ?array
    {

        if (!$this->shouldIncludeLeader())
            return null;

        if (isset($item->leader)) {
            return [
                'id' => $item->leader->id,
                'fullName' => $item->leader->fullName,
                'avatar' => $item->leader->avatar,
                'cellPhone' => $item->leader->cellPhone,
                'email' => $item->leader->email,
            ];
        }

        return null;
    }

    /**
     * Verifica se deve incluir informações do líder
     *
     * @return bool
     */
    private function shouldIncludeLeader(): bool
    {
        return $this->division && $this->division->requireLeader == 1;
    }
}
