<?php

namespace App\Models;

use App\Models\ConflictOutcome;
use App\Models\DynamicField;
use App\Models\WildlifeConflictIncident;
use App\Models\WildlifeConflictDynamicValue;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WildlifeConflictOutcome extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'wildlife_conflict_incident_id',
        'conflict_outcome_id',
    ];

    /**
     * Get the wildlife conflict incident this outcome belongs to.
     */
    public function wildlifeConflictIncident()
    {
        return $this->belongsTo(WildlifeConflictIncident::class);
    }

    /**
     * Get the conflict outcome type.
     */
    public function conflictOutcome()
    {
        return $this->belongsTo(ConflictOutcome::class);
    }

    /**
     * Get the dynamic field values for this outcome.
     */
    public function dynamicValues()
    {
        return $this->hasMany(WildlifeConflictDynamicValue::class);
    }

    /**
     * Get all dynamic fields associated with this conflict outcome type.
     */
    public function getDynamicFields()
    {
        return DynamicField::where('conflict_outcome_id', $this->conflict_outcome_id)->get();
    }
} 