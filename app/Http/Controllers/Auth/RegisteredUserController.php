<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = null;

        DB::transaction(function () use ($request, &$user) {
            // 1) Create Account
            $account = Account::create([
                'name' => $request->name . "'s Account",
                'plan' => 'free',
                'is_active' => true,
            ]);

            // 2) Create User (default account_id)
            $user = User::create([
                'account_id' => $account->id,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // 3) Add membership in account_users (owner)
            DB::table('account_users')->insert([
                'account_id' => $account->id,
                'user_id' => $user->id,
                'role' => 'owner',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        event(new Registered($user));
        Auth::login($user);

        // ✅ do NOT redirect to dashboard directly (needs workspace param)
        return redirect()->route('app');
    }
}
