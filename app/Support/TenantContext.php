<?php

namespace App\Support;

use App\Models\Account;
use App\Models\Tenant;

class TenantContext
{
    protected static ?Account $account = null;
    protected static ?Tenant $tenant = null;

    public static function set(Account $account, Tenant $tenant): void
    {
        self::$account = $account;
        self::$tenant  = $tenant;
    }

    public static function account(): ?Account
    {
        return self::$account;
    }

    public static function tenant(): ?Tenant
    {
        return self::$tenant;
    }

    public static function accountId(): ?int
    {
        return self::$account?->id;
    }

    public static function tenantId(): ?int
    {
        return self::$tenant?->id;
    }

    public static function forget(): void
    {
        self::$account = null;
        self::$tenant  = null;
    }
}
