<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['account_id','name','email','password'];

    protected $hidden = ['password','remember_token'];

   

    public function ownedWorkspaces()
    {
        return $this->hasMany(Workspace::class, 'owner_user_id');
    }

    public function workspaces()
    {
        return $this->belongsToMany(\App\Models\Workspace::class, 'workspace_users')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function accounts()
    {
        return $this->belongsToMany(\App\Models\Account::class, 'account_users')
            ->withPivot('role')
            ->withTimestamps();
    }

}
