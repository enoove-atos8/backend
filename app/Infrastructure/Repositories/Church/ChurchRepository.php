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
    public function newChurch(ChurchData $churchData, string $awsS3Bucket): Church
    {
        $church = $this->create([
            'tenant_id'           =>  $churchData->tenantId,
            'plan_id'             =>  $churchData->planId,
            'name'                =>  $churchData->name,
            'activated'           =>  $churchData->activated,
            'doc_type'            =>  $churchData->docType,
            'doc_number'          =>  $churchData->docNumber,
            'aws_s3_bucket'       =>  $awsS3Bucket,
        ]);



        throw_if(!$church, GeneralExceptions::class, 'Houve um erro ao procesar o cadastro de uma nova igreja', 500);

        return $church;
    }
}
