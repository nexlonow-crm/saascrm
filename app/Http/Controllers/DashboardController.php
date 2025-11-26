<?php

namespace App\Http\Controllers;

use App\Domain\Contacts\Models\Contact;
use App\Domain\Companies\Models\Company;
use App\Domain\Deals\Models\Deal;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // If you are using the BelongsToTenant global scope, you can simply do:
        // $contactsCount = Contact::count();
        // etc.

        $contactsCount = Contact::where('account_id', $user->account_id)
            ->where('tenant_id', $user->tenant_id)
            ->count();

        $companiesCount = Company::where('account_id', $user->account_id)
            ->where('tenant_id', $user->tenant_id)
            ->count();

        $openDealsCount = Deal::where('account_id', $user->account_id)
            ->where('tenant_id', $user->tenant_id)
            ->where('status', Deal::STATUS_OPEN)
            ->count();

        $wonDealsThisMonth = Deal::where('account_id', $user->account_id)
            ->where('tenant_id', $user->tenant_id)
            ->where('status', Deal::STATUS_WON)
            ->whereBetween('closed_at', [
                now()->startOfMonth(),
                now()->endOfMonth(),
            ])
            ->count();

        $pipelineValueOpen = Deal::where('account_id', $user->account_id)
            ->where('tenant_id', $user->tenant_id)
            ->where('status', Deal::STATUS_OPEN)
            ->sum('amount');

        return view('dashboard', [
            'contactsCount'     => $contactsCount,
            'companiesCount'    => $companiesCount,
            'openDealsCount'    => $openDealsCount,
            'wonDealsThisMonth' => $wonDealsThisMonth,
            'pipelineValueOpen' => $pipelineValueOpen,
        ]);
    }
}
