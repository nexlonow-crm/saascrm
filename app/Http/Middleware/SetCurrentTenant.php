<?php

namespace App\Http\Middleware;

use App\Models\Account;
use App\Models\Tenant;
use App\Support\TenantContext;
use Closure;
use Illuminate\Http\Request;

class SetCurrentTenant
{
    /**
     * Ensure the authenticated user has a valid account & tenant,
     * and set the tenant context for the request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            $user = auth()->user();

            // Basic safety: make sure user has account & tenant
            if (!$user->account_id || !$user->tenant_id) {
                abort(403, 'User has no account or tenant assigned.');
            }

            /** @var Account $account */
            $account = Account::find($user->account_id);
            /** @var Tenant $tenant */
            $tenant  = Tenant::where('id', $user->tenant_id)
                ->where('account_id', $user->account_id)
                ->first();

            if (!$account || !$tenant) {
                abort(403, 'Invalid tenant context.');
            }

            if ($tenant->status !== 'active') {
                abort(423, 'Tenant is not active.');
            }

            // Set global context
            TenantContext::set($account, $tenant);

            // Optionally share with all views
            view()->share('currentAccount', $account);
            view()->share('currentTenant', $tenant);
        } else {
            // Not logged in: clear context
            TenantContext::forget();
        }

        return $next($request);
    }
}
