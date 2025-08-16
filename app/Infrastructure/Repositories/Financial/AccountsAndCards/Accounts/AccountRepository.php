<?php

namespace Infrastructure\Repositories\Financial\AccountsAndCards\Accounts;

use Domain\Financial\AccountsAndCards\Accounts\DataTransferObjects\AccountData;
use Domain\Financial\AccountsAndCards\Accounts\Interfaces\AccountRepositoryInterface;
use Domain\Financial\AccountsAndCards\Accounts\Models\Accounts;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Infrastructure\Repositories\BaseRepository;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class AccountRepository extends BaseRepository implements AccountRepositoryInterface
{
    protected mixed $model = Accounts::class;

    const TABLE_NAME = 'accounts';
    const ACTIVATED_COLUMN = 'activated';

    /**
     * @inheritDoc
     * @throws UnknownProperties
     */
    public function saveAccount(AccountData $accountData): AccountData
    {
        $conditions = null;

        if($accountData->id)
        {
            $conditions = [
                'field' => self::ID_COLUMN,
                'operator' => BaseRepository::OPERATORS['EQUALS'],
                'value' => $accountData->id,
            ];
        }

        $updatedOrCreated = $this->updateOrCreate([
            'account_type'        => $accountData->accountType,
            'bank_name'           => $accountData->bankName,
            'agency_number'       => $accountData->agencyNumber,
            'account_number'      => $accountData->accountNumber,
            'activated'           => $accountData->activated,
        ], $conditions);

        return AccountData::fromResponse($updatedOrCreated->toArray());
    }


    /**
     * @throws BindingResolutionException
     */
    public function deactivateAccount(int $accountId): mixed
    {
        $conditions = [
            'field' => self::ID_COLUMN,
            'operator' => BaseRepository::OPERATORS['EQUALS'],
            'value' => $accountId
        ];
        return $this->update($conditions, ['activated' =>  false]);
    }




    /**
     * Get all cards from the database.
     *
     * @return Collection
     * @throws BindingResolutionException
     */
    public function getAccounts(): Collection
    {
        $query = function () {

            $q = DB::table(self::TABLE_NAME)
                ->where(self::ACTIVATED_COLUMN, BaseRepository::OPERATORS['EQUALS'], 1)
                ->orderBy(self::ID_COLUMN);


            $result = $q->get();
            return collect($result)->map(fn($item) => AccountData::fromResponse((array) $item));
        };

        return $this->doQuery($query);
    }
}
