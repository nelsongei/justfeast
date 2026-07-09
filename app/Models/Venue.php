<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venue extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'map_data',
        'seating_layout',
    ];

    protected $casts = [
        'map_data' => 'array',
        'seating_layout' => 'array',
    ];

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}
