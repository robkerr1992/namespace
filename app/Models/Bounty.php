<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Bounty extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function winningSubmission(): HasOne
    {
        return $this->hasOne(Submission::class)->where('won_at', '!=', null);
    }

    public static function scopeActive(Builder $query): Builder
    {
        return $query->whereDoesntHave('winningSubmission');
    }

    public static function scopeCompleted(Builder $query): Builder
    {
        return $query->whereHas('winningSubmission');
    }
}
