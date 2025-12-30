<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Domain\Deals\Models\Deal;
use App\Domain\Deals\Models\Stage;
use App\Domain\Contacts\Models\Contact;
use App\Domain\Activities\Models\Activity;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Provided by workspace middleware
        $workspace = app('currentWorkspace');

        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $startOfWeek  = Carbon::now()->startOfWeek(); // Monday by default

        /**
         * Because of BelongsToWorkspace global scope:
         * Deal::query() is already filtered by workspace_id automatically.
         */

        // Base queries
        $baseDealQuery = Deal::query();
        $baseActivityQuery = Activity::query()->where('owner_id', $user->id); // my activities
        $baseContactQuery = Contact::query();

        // KPI cards
        $openDealsCount  = (clone $baseDealQuery)->where('status', 'open')->count();
        $openDealsAmount = (clone $baseDealQuery)->where('status', 'open')->sum('amount');

        $wonThisMonthCount  = (clone $baseDealQuery)
            ->where('status', 'won')
            ->where('updated_at', '>=', $startOfMonth)
            ->count();

        $wonThisMonthAmount = (clone $baseDealQuery)
            ->where('status', 'won')
            ->where('updated_at', '>=', $startOfMonth)
            ->sum('amount');

        $lostThisMonthCount = (clone $baseDealQuery)
            ->where('status', 'lost')
            ->where('updated_at', '>=', $startOfMonth)
            ->count();

        $activitiesDueTodayCount = (clone $baseActivityQuery)
            ->whereDate('due_date', $today)
            ->where('is_completed', false)
            ->count();

        $activitiesOverdueCount = (clone $baseActivityQuery)
            ->whereDate('due_date', '<', $today)
            ->where('is_completed', false)
            ->count();

        $newContactsThisWeek = (clone $baseContactQuery)
            ->where('created_at', '>=', $startOfWeek)
            ->count();

        // Chart 1: Deals by Stage (count + amount)
        $dealsByStageRaw = (clone $baseDealQuery)
            ->selectRaw('stage_id, COUNT(*) as total, SUM(amount) as amount')
            ->groupBy('stage_id')
            ->get();

        $stageIds = $dealsByStageRaw->pluck('stage_id')->filter()->unique();
        $stages = Stage::whereIn('id', $stageIds)->get()->keyBy('id');

        $stageLabels = [];
        $stageDealCounts = [];
        $stageDealAmounts = [];

        foreach ($dealsByStageRaw as $row) {
            $stage = $stages->get($row->stage_id);
            // your stages use name (not label) in updated migration
            $stageLabels[]      = $stage ? ($stage->name ?? 'Unknown') : 'Unknown';
            $stageDealCounts[]  = (int) $row->total;
            $stageDealAmounts[] = (float) ($row->amount ?? 0);
        }

        // Chart 2: Won amount by month (last 6 months)
        $months = [];
        $current = Carbon::now()->startOfMonth()->copy()->subMonths(5);

        for ($i = 0; $i < 6; $i++) {
            $months[] = $current->copy();
            $current->addMonth();
        }

        // MySQL version (DATE_FORMAT)
        $wonByMonthRaw = (clone $baseDealQuery)
            ->where('status', 'won')
            ->where('updated_at', '>=', $months[0]->copy()->startOfMonth())
            ->selectRaw('DATE_FORMAT(updated_at, "%Y-%m") as ym, SUM(amount) as total')
            ->groupBy('ym')
            ->orderBy('ym')
            ->get()
            ->keyBy('ym');

        $wonMonthLabels = [];
        $wonMonthTotals = [];

        foreach ($months as $month) {
            $key = $month->format('Y-m');
            $wonMonthLabels[] = $month->format('M Y');
            $wonMonthTotals[] = (float) (optional($wonByMonthRaw->get($key))->total ?? 0.0);
        }

        // Chart 3: Activities by type (my activities)
        $activitiesByTypeRaw = (clone $baseActivityQuery)
            ->selectRaw('type, COUNT(*) as total')
            ->groupBy('type')
            ->get()
            ->keyBy('type');

        $activityTypes = ['task', 'call', 'meeting', 'email'];
        $activityTypeLabels = [];
        $activityTypeTotals = [];

        foreach ($activityTypes as $type) {
            $activityTypeLabels[] = ucfirst($type);
            $activityTypeTotals[] = (int) (optional($activitiesByTypeRaw->get($type))->total ?? 0);
        }

        return view('dashboard', [
            'workspace'               => $workspace, // useful in UI header / switcher

            'openDealsCount'          => $openDealsCount,
            'openDealsAmount'         => $openDealsAmount,
            'wonThisMonthCount'       => $wonThisMonthCount,
            'wonThisMonthAmount'      => $wonThisMonthAmount,
            'lostThisMonthCount'      => $lostThisMonthCount,
            'activitiesDueTodayCount' => $activitiesDueTodayCount,
            'activitiesOverdueCount'  => $activitiesOverdueCount,
            'newContactsThisWeek'     => $newContactsThisWeek,

            'stageLabels'             => $stageLabels,
            'stageDealCounts'         => $stageDealCounts,
            'stageDealAmounts'        => $stageDealAmounts,

            'wonMonthLabels'          => $wonMonthLabels,
            'wonMonthTotals'          => $wonMonthTotals,

            'activityTypeLabels'      => $activityTypeLabels,
            'activityTypeTotals'      => $activityTypeTotals,
        ]);
    }
}
