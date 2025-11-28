<?php

namespace App\Domain\Deals\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stage extends Model
{
    use HasFactory;

    protected $fillable = [
        'pipeline_id',
        'name',
        'label',
        'badge_color',
        'probability',
        'position',
    ];

    // Helper: return a safe badge color
    public function badgeColor(): string
    {
        return $this->badge_color ?: 'secondary';
    }
    // Helper: what text to show inside badge
    public function displayName(): string
    {
        return $this->label ?: $this->name;
    }

    public function pipeline()
    {
        return $this->belongsTo(Pipeline::class);
    }

    public function deals()
    {
        return $this->hasMany(Deal::class);
    }
}
