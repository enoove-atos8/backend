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

    const TYPE = 'type';

    const STRIPE_PRICE = 'stripe_price';

    const QUANTITY = 'quantity';

    const ENDS_AT = 'ends_at';

    const UPDATED_AT = 'updated_at';

    const CREATED_AT = 'created_at';

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
     *
     * @param  array  $stripeSubscription  Raw Stripe subscription data
     * @return array Processed subscription data that was saved
     */
    public function saveSubscription(int $churchId, array $stripeSubscription): array
    {
        return tenancy()->central(function () use ($churchId, $stripeSubscription) {
            // Mapear dados do Stripe para o formato do banco
            $subscriptionData = [
                self::TYPE => 'default',
                self::STRIPE_ID_COLUMN => $stripeSubscription['id'],
                self::STRIPE_STATUS_COLUMN => $stripeSubscription['status'],
                self::STRIPE_PRICE => null,
                self::QUANTITY => $stripeSubscription['items']['data'][0]['quantity'] ?? 1,
                self::TRIAL_ENDS_AT_COLUMN => isset($stripeSubscription['trial_end'])
                    ? date('Y-m-d H:i:s', $stripeSubscription['trial_end'])
                    : null,
                self::ENDS_AT => null,
                self::UPDATED_AT => now(),
                self::CREATED_AT => DB::raw('COALESCE(created_at, NOW())'),
            ];

            DB::table(self::TABLE_NAME)->updateOrInsert(
                [
                    self::BILLABLE_TYPE_COLUMN => Church::class,
                    self::BILLABLE_ID_COLUMN => $churchId,
                ],
                $subscriptionData
            );

            return $subscriptionData;
        });
    }
}
