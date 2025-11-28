<?php
namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $activities = Activity::where('tenant_id', $user->tenant_id)
            ->where('account_id', $user->account_id)
            ->where('owner_id', $user->id)
            ->orderBy('due_date', 'asc')
            ->get();

        return view('activities.index', compact('activities'));
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'subject_type' => 'required|string',
            'subject_id'   => 'required|integer',
            'type'         => 'required|string',
            'title'        => 'required|string|max:255',
            'notes'        => 'nullable|string',
            'due_date'     => 'nullable|date',
        ]);

        $data['account_id'] = $user->account_id;
        $data['tenant_id']  = $user->tenant_id;
        $data['owner_id']   = $user->id;

        Activity::create($data);

        return back()->with('status', 'Activity added.');
    }

    public function update(Request $request, Activity $activity)
    {
        $data = $request->validate([
            'title'    => 'required|string',
            'type'     => 'required|string',
            'notes'    => 'nullable|string',
            'due_date' => 'nullable|date',
        ]);

        $activity->update($data);

        return back()->with('status', 'Activity updated.');
    }

    public function destroy(Activity $activity)
    {
        $activity->delete();

        return back()->with('status', 'Activity deleted.');
    }
}
