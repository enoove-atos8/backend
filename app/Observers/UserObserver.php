<?php

namespace App\Observers;

use App\Domain\Accounts\Users\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    /**
     * Handle the User "created" event.
     * Syncs user to global users table for faster tenant discovery
     */
    public function created(User $user): void
    {
        try {
            $tenantId = tenant('id');

            if ($tenantId) {
                DB::connection('mysql')->table('users')->updateOrInsert(
                    [
                        'email' => $user->email,
                        'tenant_id' => $tenantId,
                    ],
                    [
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }
        } catch (\Exception $e) {
            Log::error('Failed to sync user to global table on create', [
                'email' => $user->email,
                'tenant_id' => $tenantId ?? null,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle the User "updated" event.
     * Updates global users table if email changes
     */
    public function updated(User $user): void
    {
        try {
            $tenantId = tenant('id');

            if ($tenantId && $user->isDirty('email')) {
                // Remove old email for this tenant
                $original = $user->getOriginal('email');
                DB::connection('mysql')->table('users')
                    ->where('email', $original)
                    ->where('tenant_id', $tenantId)
                    ->delete();

                // Insert new email for this tenant
                DB::connection('mysql')->table('users')->updateOrInsert(
                    [
                        'email' => $user->email,
                        'tenant_id' => $tenantId,
                    ],
                    [
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }
        } catch (\Exception $e) {
            Log::error('Failed to sync user to global table on update', [
                'email' => $user->email,
                'tenant_id' => $tenantId ?? null,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle the User "deleted" event.
     * Removes user from global users table
     */
    public function deleted(User $user): void
    {
        try {
            $tenantId = tenant('id');

            if ($tenantId) {
                DB::connection('mysql')->table('users')
                    ->where('email', $user->email)
                    ->where('tenant_id', $tenantId)
                    ->delete();
            }
        } catch (\Exception $e) {
            Log::error('Failed to remove user from global table on delete', [
                'email' => $user->email,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
