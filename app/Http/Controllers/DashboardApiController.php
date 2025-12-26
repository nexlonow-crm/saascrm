<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Adjust these namespaces to match your app:
use App\Domain\Deals\Models\Deal;
use App\Domain\Deals\Models\PipelineStage; // if your stage model is Stage, change this
use App\Domain\Activities\Models\Activity;

class DashboardApiController extends Controller
{
    public function kpis(Request $request)
    {
        $user = $request->user();

        $baseDeals = Deal::query()
            ->where('account_id', $user->account_id)
            ->where('tenant_id', $user->tenant_id);

        $totalDeals = (clone $baseDeals)->count();
        $openDeals  = (clone $baseDeals)->where('status', 'open')->count();
        $wonDeals   = (clone $baseDeals)->where('status', 'won')->count();
        $lostDeals  = (clone $baseDeals)->where('status', 'lost')->count();

        $pipelineValueOpen = (clone $baseDeals)->where('status', 'open')->sum('amount');
        $pipelineValueWon  = (clone $baseDeals)->where('status', 'won')->sum('amount');

        return response()->json([
            'totalDeals' => $totalDeals,
            'openDeals'  => $openDeals,
            'wonDeals'   => $wonDeals,
            'lostDeals'  => $lostDeals,
            'openValue'  => (float) $pipelineValueOpen,
            'wonValue'   => (float) $pipelineValueWon,
        ]);
    }

    public function dealsByStage(Request $request)
    {
        $user = $request->user();

        // IMPORTANT: adjust model name if yours is Stage instead of PipelineStage
        $rows = PipelineStage::query()
            ->where('account_id', $user->account_id)
            ->where('tenant_id', $user->tenant_id)
            ->orderBy('position')
            ->get(['id', 'name', 'label', 'badge_color']);

        $counts = Deal::query()
            ->where('account_id', $user->account_id)
            ->where('tenant_id', $user->tenant_id)
            ->where('status', 'open')
            ->select('stage_id', DB::raw('COUNT(*) as cnt'), DB::raw('COALESCE(SUM(amount),0) as total'))
            ->groupBy('stage_id')
            ->get()
            ->keyBy('stage_id');

        $data = $rows->map(function ($stage) use ($counts) {
            $agg = $counts->get($stage->id);

            return [
                'stage_id' => $stage->id,
                'name'     => $stage->label ?: $stage->name,
                'count'    => (int) ($agg->cnt ?? 0),
                'total'    => (float) ($agg->total ?? 0),
            ];
        })->values();

        return response()->json($data);
    }

    public function dealsTrend(Request $request)
    {
        $user = $request->user();

        // last 30 days: deals created per day
        $rows = Deal::query()
            ->where('account_id', $user->account_id)
            ->where('tenant_id', $user->tenant_id)
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw("DATE(created_at) as day, COUNT(*) as cnt")
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        return response()->json([
            'labels' => $rows->pluck('day'),
            'data'   => $rows->pluck('cnt'),
        ]);
    }

    public function activitiesSummary(Request $request)
    {
        $user = $request->user();

        $base = Activity::query()
            ->where('account_id', $user->account_id)
            ->where('tenant_id', $user->tenant_id);

        $total = (clone $base)->count();
        $open  = (clone $base)->where('is_completed', false)->count();
        $done  = (clone $base)->where('is_completed', true)->count();
        $overdue = (clone $base)
            ->where('is_completed', false)
            ->whereNotNull('due_date')
            ->where('due_date', '<', now())
            ->count();

        return response()->json([
            'total'   => $total,
            'open'    => $open,
            'done'    => $done,
            'overdue' => $overdue,
        ]);
    }
}
