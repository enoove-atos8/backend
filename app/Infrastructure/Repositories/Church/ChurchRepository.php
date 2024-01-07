<?php

namespace Infrastructure\Repositories\Church;

use Domain\Churches\Interfaces\ChurchRepositoryInterface;
use Domain\Churches\DataTransferObjects\ChurchData;
use Domain\Churches\Models\Church;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Infrastructure\Repositories\BaseRepository;
use Throwable;

class ChurchRepository extends BaseRepository implements ChurchRepositoryInterface
{
    protected mixed $model = Church::class;

    const TENANT_ID_COLUMN = 'tenant_id';

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
     * @param null $tenantId
     * @return Model
     * @throws BindingResolutionException
     */
    public function getChurch($tenantId): Model
    {
        return $this->getItemByColumn(self::TENANT_ID_COLUMN, $tenantId);
    }
}
