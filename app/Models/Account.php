<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = ['name', 'plan'];

    public function tenants()
    {
        return $this->hasMany(Tenant::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
