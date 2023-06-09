<?php

namespace Infrastructure\Repositories\Church;

use Domain\Churches\Interfaces\ChurchRepositoryInterface;
use Domain\Churches\DataTransferObjects\ChurchData;
use Domain\Churches\Models\Church;
use Domain\Churches\Models\Tenant;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Infrastructure\Exceptions\GeneralExceptions;
use Infrastructure\Repositories\BaseRepository;
use Throwable;

class ChurchRepository extends BaseRepository implements ChurchRepositoryInterface
{
    protected mixed $model = Church::class;

    /**
     * @throws Throwable
     */
    public function newChurch(ChurchData $churchData): Church
    {
        /*$relationshipTable = 'tenants';
        $relationshipsConditions = [
            ['leftColumn' => 'churches.tenant_id', 'op' => $this::EQUALS, 'rightColumn' => 'tenants.id'],
        ];
        $conditions = [
            ['tenants.id', $this::EQUALS, 'ibop']
        ];
        $columns = ['churches.id', 'churches.name', 'tenants.id'];

        $test = $this->getItemWithRelationships($relationshipTable, $relationshipsConditions, $columns, $conditions);*/


        $church = $this->create([
            'tenant_id'                 =>  $churchData->tenantId,
            'name'                      =>  $churchData->name,
            'activated'                 =>  $churchData->activated,
            'doc_type'                  =>  $churchData->docType,
            'doc_number'                =>  $churchData->docNumber,
        ]);



        throw_if(!$church, GeneralExceptions::class, 'Houve um erro ao procesar o cadastro de uma nova igreja', 500);

        return $church;
    }
}
