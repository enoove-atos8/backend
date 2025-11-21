<?php

namespace App\Infrastructure\Repositories\CentralDomain\Church;

use Domain\CentralDomain\Churches\Church\DataTransferObjects\ChurchData;
use Domain\CentralDomain\Churches\Church\Interfaces\ChurchRepositoryInterface;
use Domain\CentralDomain\Churches\Church\Models\Church;
use Domain\CentralDomain\Plans\DataTransferObjects\PlanData;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Infrastructure\Repositories\BaseRepository;
use Infrastructure\Repositories\CentralDomain\PlanRepository;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Throwable;

class ChurchRepository extends BaseRepository implements ChurchRepositoryInterface
{
    protected mixed $model = Church::class;

    const TABLE_NAME = 'churches';
    const ID_COLUMN = 'id';
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
            'logo'                =>  $churchData->logo,
            'address'             =>  $churchData->address,
            'cell_phone'          =>  $churchData->cellPhone,
            'mail'                =>  $churchData->mail,
            'doc_type'            =>  $churchData->docType,
            'doc_number'          =>  $churchData->docNumber,
            'aws_s3_bucket'       =>  $awsS3Bucket,
            'stripe_id'           =>  $churchData->stripeId,
        ]);
    }


    /**
     * @param string $tenantId
     * @return ChurchData|null
     * @throws UnknownProperties
     */
    public function getChurch(string $tenantId): ?ChurchData
    {
        return tenancy()->central(function () use ($tenantId){
            $result = DB::table(ChurchRepository::TABLE_NAME)
                ->where(self::TENANT_ID_COLUMN, BaseRepository::OPERATORS['EQUALS'], $tenantId)
                ->where(self::ACTIVATED_COLUMN, BaseRepository::OPERATORS['EQUALS'], true)
                ->first();

            return $result ? ChurchData::fromResponse((array) $result) : null;
        });
    }

    /**
     * @param int $churchId
     * @return ChurchData|null
     * @throws UnknownProperties
     */
    public function getChurchById(int $churchId): ?ChurchData
    {
        return tenancy()->central(function () use ($churchId) {
            $result = DB::table(self::TABLE_NAME)
                ->where(self::ID_COLUMN, BaseRepository::OPERATORS['EQUALS'], $churchId)
                ->first();

            return $result ? ChurchData::fromResponse((array) $result) : null;
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
     * @param int $id
     * @return Collection
     * @throws BindingResolutionException
     */
    public function getChurchesByPlanId(int $id): Collection
    {
        return tenancy()->central(function () use ($id){

            $this->queryConditions = [];
            $this->queryConditions [] = $this->whereEqual(self::PLAN_ID_COLUMN, $id, 'and');

            return $this->getItemsWithRelationshipsAndWheres($this->queryConditions);
        });
    }

    /**
     * @param int $churchId
     * @return PlanData|null
     * @throws UnknownProperties
     */
    public function getChurchPlan(int $churchId): ?PlanData
    {
        return tenancy()->central(function () use ($churchId) {
            $result = DB::table(self::TABLE_NAME)
                ->join(
                    PlanRepository::TABLE_NAME,
                    self::TABLE_NAME . '.' . self::PLAN_ID_COLUMN,
                    BaseRepository::OPERATORS['EQUALS'],
                    PlanRepository::TABLE_NAME . '.' . PlanRepository::ID_COLUMN
                )
                ->where(self::TABLE_NAME . '.' . self::ID_COLUMN, BaseRepository::OPERATORS['EQUALS'], $churchId)
                ->select(PlanRepository::TABLE_NAME . '.*')
                ->first();

            return $result ? PlanData::fromResponse((array) $result) : null;
        });
    }
}
