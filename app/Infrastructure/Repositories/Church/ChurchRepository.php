<?php

namespace Infrastructure\Repositories\Church;

use Domain\CentralDomain\Churches\Church\DataTransferObjects\ChurchData;
use Domain\CentralDomain\Churches\Church\Interfaces\ChurchRepositoryInterface;
use Domain\CentralDomain\Churches\Church\Models\Church;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Infrastructure\Repositories\BaseRepository;
use Throwable;

class ChurchRepository extends BaseRepository implements ChurchRepositoryInterface
{
    protected mixed $model = Church::class;

    const TENANT_ID_COLUMN = 'tenant_id';
    const PLAN_ID_COLUMN = 'plan_id';
    const ACTIVATED_COLUMN = 'activated';

    /**
     * Array of conditions
     */
    private array $queryConditions = [];

    /**
     * Array of where, between and another clauses that was mounted dynamically
     */
    private array $queryClausesAndConditions = [
        'where_clause'    =>  [
            'exists' => false,
            'clause'   =>  [],
        ]
    ];

    /**
     * @throws Throwable
     */
    public function newChurch(ChurchData $churchData, string $awsS3Bucket): Church
    {
        return $this->create([
            'tenant_id'           =>  $churchData->tenantId,
            'plan_id'             =>  $churchData->planId,
            'name'                =>  $churchData->name,
            'activated'           =>  $churchData->activated,
            'doc_type'            =>  $churchData->docType,
            'doc_number'          =>  $churchData->docNumber,
            'aws_s3_bucket'       =>  $awsS3Bucket,
        ]);
    }


    /**
     * @param string $tenantId
     * @return Church|Model
     * @throws BindingResolutionException
     */
    public function getChurch(string $tenantId): Church | Model
    {
        return tenancy()->central(function () use ($tenantId){
            return $this->getItemByColumn(self::TENANT_ID_COLUMN, BaseRepository::OPERATORS['EQUALS'], $tenantId);
        });
    }


    /**
     * @return Collection
     * @throws BindingResolutionException
     */
    public function getChurches(): Collection
    {
        return tenancy()->central(function (){

            $this->queryConditions = [];
            $this->queryConditions [] = $this->whereEqual(self::ACTIVATED_COLUMN, true, 'and');

            return $this->getItemsWithRelationshipsAndWheres($this->queryConditions);
        });
    }


    /**
     * @param null $planId
     * @return Collection
     * @throws BindingResolutionException
     */
    public function getChurchesByPlanId(null $planId): Collection
    {
        return tenancy()->central(function () use ($planId){

            $this->queryConditions = [];
            $this->queryConditions [] = $this->whereEqual(self::PLAN_ID_COLUMN, $planId, 'and');

            return $this->getItemsWithRelationshipsAndWheres($this->queryConditions);
        });
    }
}
