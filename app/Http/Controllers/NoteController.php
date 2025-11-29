<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'subject_type' => ['required', 'string'],
            'subject_id'   => ['required', 'integer'],
            'body'         => ['required', 'string'],
            'is_pinned'    => ['nullable', 'boolean'],
        ]);

        $data['account_id'] = $user->account_id;
        $data['tenant_id']  = $user->tenant_id;
        $data['user_id']    = $user->id;
        $data['is_pinned']  = $request->boolean('is_pinned');

        Note::create($data);

        return back()->with('status', 'Note added.');
    }

    public function destroy(Note $note)
    {
        // TODO: add tenant/account ownership checks if needed
        $note->delete();

        return back()->with('status', 'Note deleted.');
    }
}
