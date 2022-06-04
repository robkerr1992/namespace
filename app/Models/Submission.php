<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;

class Submission extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function bounty(): BelongsTo
    {
        return $this->belongsTo(Bounty::class);
    }

    public function isWinningSubmission(): bool
    {
        return boolval($this->won_at);
    }
}
