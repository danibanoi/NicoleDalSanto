<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name', 'slug', 'category', 'tagline', 'description', 'price_cents', 'duration_weeks',
    'sessions_per_week', 'level', 'mode', 'features', 'program', 'color', 'is_featured', 'is_active', 'sort',
])]
class Package extends Model
{
    protected function casts(): array
    {
        return [
            'features' => 'array',
            'program' => 'array',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(TrainingSession::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
