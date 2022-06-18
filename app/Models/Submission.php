<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Submission extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function bounty(): BelongsTo
    {
        return $this->belongsTo(Bounty::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function submitter(): BelongsTo
    {
        return $this->user();
    }

    public static function scopeActive(Builder $query): Builder
    {
        return $query->whereHas('bounty', function (Builder $query2) {
            $query2->where('deadline', '>', \Carbon\Carbon::now()->timestamp)
                ->whereDoesntHave('winningSubmission');
        });
    }

    public function isWinningSubmission(): bool
    {
        return boolval($this->won_at);
    }
}
