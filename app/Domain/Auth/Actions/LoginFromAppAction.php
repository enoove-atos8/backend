<?php

namespace Domain\Auth\Actions;

use Domain\Auth\DataTransferObjects\AuthData;
use Domain\CentralDomain\Churches\Church\Actions\GetChurchAction;
use Domain\CentralDomain\Churches\Church\Actions\GetChurchesAction;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Infrastructure\Traits\Roles\HasAuthorization;
use Throwable;

class LoginFromAppAction
{
    use HasAuthorization;

    private GetChurchAction $getChurchAction;

    private GetChurchesAction $getChurchesAction;

    public function __construct(
        GetChurchAction $getChurchAction,
        GetChurchesAction $getChurchesAction
    ) {
        $this->getChurchAction = $getChurchAction;
        $this->getChurchesAction = $getChurchesAction;
    }

    /**
     * @throws Throwable
     */
    public function execute(AuthData $authData): array|Authenticatable|null
    {
        // Step 1: Try to find tenant(s) from global users table (fast path)
        $tenantIds = $this->findTenantsFromGlobalTable($authData->email);

        if (! empty($tenantIds)) {
            // Try login in all tenants found for this email
            foreach ($tenantIds as $tenantId) {
                $result = $this->attemptLoginForTenant($tenantId, $authData);
                if ($result !== null) {
                    return $result;
                }
            }
        }

        // Step 2: Fallback to checking all tenants (slow path)
        Log::info('User not found in global table, falling back to full tenant scan', [
            'email' => $authData->email,
        ]);

        $churches = $this->getChurchesAction->execute();
        $tenantOfUser = null;

        foreach ($churches as $church) {
            tenancy()->initialize($church->tenantId);

            if (Auth::attempt($authData->toArray())) {
                $user = auth()->user();
                $userDetail = $user->detail()->first();
                $userRoles = [];

                foreach ($user->roles()->get() as $role) {
                    $userRoles[] = $role->name;
                }

                $church = $this->getChurchAction->execute($church->tenantId);

                if ($user->activated) {
                    $token = $user->createToken('app', $userRoles, null)->plainTextToken;
                    $user->token = $token;
                    $tenantOfUser = $church->tenantId;

                    // Sync to global table for next time
                    $this->syncToGlobalTable($authData->email, $church->tenantId);

                    return [
                        'user' => $user,
                        'userDetail' => $userDetail,
                        'church' => $church,
                    ];
                } else {
                    return ['error' => false, 'status' => 401];
                }
            }
        }

        if (is_null($tenantOfUser)) {
            return ['error' => false, 'status' => 404];
        }
    }

    /**
     * Find tenant IDs from global users table (supports multiple tenants per email)
     */
    private function findTenantsFromGlobalTable(string $email): array
    {
        try {
            $users = DB::connection('mysql')->table('users')
                ->where('email', $email)
                ->pluck('tenant_id')
                ->toArray();

            return $users;
        } catch (\Exception $e) {
            Log::error('Failed to query global users table', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Attempt login for a specific tenant
     */
    private function attemptLoginForTenant(string $tenantId, AuthData $authData): ?array
    {
        try {
            tenancy()->initialize($tenantId);

            if (Auth::attempt($authData->toArray())) {
                $user = auth()->user();
                $userDetail = $user->detail()->first();
                $userRoles = [];

                foreach ($user->roles()->get() as $role) {
                    $userRoles[] = $role->name;
                }

                $church = $this->getChurchAction->execute($tenantId);

                if ($user->activated) {
                    $token = $user->createToken('app', $userRoles, null)->plainTextToken;
                    $user->token = $token;

                    return [
                        'user' => $user,
                        'userDetail' => $userDetail,
                        'church' => $church,
                    ];
                } else {
                    return ['error' => false, 'status' => 401];
                }
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Failed to attempt login for tenant', [
                'tenant_id' => $tenantId,
                'email' => $authData->email,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Sync user to global table after successful login
     */
    private function syncToGlobalTable(string $email, string $tenantId): void
    {
        try {
            DB::connection('mysql')->table('users')->updateOrInsert(
                [
                    'email' => $email,
                    'tenant_id' => $tenantId,
                ],
                [
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        } catch (\Exception $e) {
            Log::error('Failed to sync user to global table after login', [
                'email' => $email,
                'tenant_id' => $tenantId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
