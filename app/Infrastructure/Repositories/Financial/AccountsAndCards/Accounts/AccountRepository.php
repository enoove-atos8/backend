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

    const ID_COLUMN_JOINED = 'accounts.id';


    const DISPLAY_SELECT_COLUMNS = [
        'accounts.id as accounts_id',
        'accounts.account_type as accounts_account_type',
        'accounts.bank_name as accounts_bank_name',
        'accounts.agency_number as accounts_agency_number',
        'accounts.account_number as accounts_account_number',
        'accounts.activated as accounts_activated',
    ];

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
     * @param bool $returnDeactivatesAccounts
     * @return Collection
     * @throws BindingResolutionException
     */
    public function getAccounts(bool $returnDeactivatesAccounts): Collection
    {
        $query = function () use ($returnDeactivatesAccounts) {

            $q = DB::table(self::TABLE_NAME);

            if(!$returnDeactivatesAccounts)
                $q->where(self::ACTIVATED_COLUMN, BaseRepository::OPERATORS['EQUALS'], true);

            $q->orderBy(self::ID_COLUMN);


            $result = $q->get();
            return collect($result)->map(fn($item) => AccountData::fromResponse((array) $item));
        };

        return $this->doQuery($query);
    }


    /**
     * Get an account by id
     *
     * @param int $id
     * @return AccountData|null
     * @throws BindingResolutionException
     */
    public function getAccountsById(int $id): ?AccountData
    {
        $query = function () use ($id){

            $q = DB::table(self::TABLE_NAME)
                ->where(self::ID_COLUMN, BaseRepository::OPERATORS['EQUALS'], $id);


            $result = $q->first();
            return $result ? AccountData::fromResponse((array) $result) : null;
        };

        return $this->doQuery($query);
    }
}
