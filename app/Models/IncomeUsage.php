<?php

namespace App\Models;

use App\Models\Organisation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncomeUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'organisation_id',
        'period',
        'month',
        'administration_amount',
        'management_activities_amount',
        'social_services_amount',
        'law_enforcement_amount',
        'other_amount',
        'other_description',
    ];

    protected $casts = [
        'period' => 'integer',
        'month' => 'integer',
        'administration_amount' => 'decimal:2',
        'management_activities_amount' => 'decimal:2',
        'social_services_amount' => 'decimal:2',
        'law_enforcement_amount' => 'decimal:2',
        'other_amount' => 'decimal:2',
    ];

    /**
     * Get the organisation that owns this income usage record.
     */
    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    /**
     * Get the total expenditure for this record
     */
    public function getTotalExpenditureAttribute()
    {
        return $this->administration_amount +
            $this->management_activities_amount +
            $this->social_services_amount +
            $this->law_enforcement_amount +
            $this->other_amount;
    }
} 