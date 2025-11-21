<?php

namespace Infrastructure\Repositories\CentralDomain;

use Domain\CentralDomain\Billing\DataTransferObjects\SubscriptionData;
use Domain\CentralDomain\Billing\Interfaces\SubscriptionRepositoryInterface;
use Domain\CentralDomain\Churches\Church\Models\Church;
use Illuminate\Support\Facades\DB;
use Infrastructure\Repositories\BaseRepository;
use Laravel\Cashier\Subscription;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class SubscriptionRepository extends BaseRepository implements SubscriptionRepositoryInterface
{
    protected mixed $model = Subscription::class;

    const TABLE_NAME = 'subscriptions';
    const ID_COLUMN = 'id';
    const BILLABLE_TYPE_COLUMN = 'billable_type';
    const BILLABLE_ID_COLUMN = 'billable_id';
    const STRIPE_ID_COLUMN = 'stripe_id';
    const STRIPE_STATUS_COLUMN = 'stripe_status';
    const TRIAL_ENDS_AT_COLUMN = 'trial_ends_at';

    /**
     * @param int $churchId
     * @return SubscriptionData|null
     * @throws UnknownProperties
     */
    public function getChurchSubscription(int $churchId): ?SubscriptionData
    {
        return tenancy()->central(function () use ($churchId) {
            $result = DB::table(self::TABLE_NAME)
                ->where(self::BILLABLE_TYPE_COLUMN, BaseRepository::OPERATORS['EQUALS'], Church::class)
                ->where(self::BILLABLE_ID_COLUMN, BaseRepository::OPERATORS['EQUALS'], $churchId)
                ->first();

            return $result ? SubscriptionData::fromResponse((array) $result) : null;
        });
    }
}
