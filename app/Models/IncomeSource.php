<?php

namespace App\Models;

use App\Models\Organisation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncomeSource extends Model
{
    use HasFactory;

    protected $fillable = [
        'organisation_id',
        'period',
        'month',
        'trophy_fee_amount',
        'hides_amount',
        'meat_amount',
        'hunting_concession_fee_amount',
        'photographic_fee_amount',
        'other_amount',
        'other_description'
    ];

    protected $casts = [
        'period' => 'integer',
        'month' => 'integer',
        'trophy_fee_amount' => 'decimal:2',
        'hides_amount' => 'decimal:2',
        'meat_amount' => 'decimal:2',
        'hunting_concession_fee_amount' => 'decimal:2',
        'photographic_fee_amount' => 'decimal:2',
        'other_amount' => 'decimal:2',
    ];

    /**
     * Get the organisation that owns this income source.
     */
    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    /**
     * Get the total income for this record
     */
    public function getTotalIncomeAttribute()
    {
        return $this->trophy_fee_amount +
            $this->hides_amount +
            $this->meat_amount +
            $this->hunting_concession_fee_amount +
            $this->photographic_fee_amount +
            $this->other_amount;
    }
} 