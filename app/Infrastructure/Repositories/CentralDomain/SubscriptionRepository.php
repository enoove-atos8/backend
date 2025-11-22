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

    /**
     * Save or update church subscription
     */
    public function saveSubscription(int $churchId, array $subscriptionData): bool
    {
        return tenancy()->central(function () use ($churchId, $subscriptionData) {
            try {
                DB::table(self::TABLE_NAME)->updateOrInsert(
                    [
                        self::BILLABLE_TYPE_COLUMN => Church::class,
                        self::BILLABLE_ID_COLUMN => $churchId,
                    ],
                    [
                        'type' => $subscriptionData['type'] ?? 'default',
                        self::STRIPE_ID_COLUMN => $subscriptionData['stripe_id'],
                        self::STRIPE_STATUS_COLUMN => $subscriptionData['stripe_status'],
                        'stripe_price' => $subscriptionData['stripe_price'] ?? null,
                        'quantity' => $subscriptionData['quantity'] ?? 1,
                        self::TRIAL_ENDS_AT_COLUMN => $subscriptionData['trial_ends_at'] ?? null,
                        'ends_at' => $subscriptionData['ends_at'] ?? null,
                        'updated_at' => now(),
                        'created_at' => DB::raw('COALESCE(created_at, NOW())'),
                    ]
                );

                return true;
            } catch (\Exception $e) {
                return false;
            }
        });
    }
}
