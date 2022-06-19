<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Bounty extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function poster(): BelongsTo
    {
        return $this->user();
    }

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

    public function deadlineHasPassed(): bool
    {
        return \Carbon\Carbon::now()->timestamp > $this->getAttribute('deadline');
    }

    public function claimed(): bool
    {
        return $this->getAttribute('claimed');
    }
}
