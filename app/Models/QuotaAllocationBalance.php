<?php

namespace App\Models;

use App\Models\Organisation;
use App\Models\Species;
use App\Models\QuotaAllocation;
use App\Models\HuntingActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuotaAllocationBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'organisation_id',
        'species_id',
        'period',
        'quota_allocation_id',
        'allocated_quota',
        'total_off_take',
        'remaining_quota'
    ];

    protected $casts = [
        'allocated_quota' => 'integer',
        'total_off_take' => 'integer',
        'remaining_quota' => 'integer',
    ];

    /**
     * Get the organization that owns the quota allocation balance.
     */
    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }

    /**
     * Get the species associated with the quota allocation balance.
     */
    public function species(): BelongsTo
    {
        return $this->belongsTo(Species::class);
    }

    /**
     * Get the quota allocation associated with the quota allocation balance.
     */
    public function quotaAllocation(): BelongsTo
    {
        return $this->belongsTo(QuotaAllocation::class);
    }
}
