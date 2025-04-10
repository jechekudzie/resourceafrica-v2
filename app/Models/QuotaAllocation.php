<?php

namespace App\Models;

use App\Models\Organisation;
use App\Models\Species;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuotaAllocation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organisation_id',
        'species_id',
        'hunting_quota',
        'rational_killing_quota',
        'start_date',
        'end_date',
        'period',
        'notes'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Get the organisation that owns the quota allocation.
     */
    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    /**
     * Get the species associated with the quota allocation.
     */
    public function species()
    {
        return $this->belongsTo(Species::class);
    }
}
