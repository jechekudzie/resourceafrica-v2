<?php

namespace App\Models;

use App\Models\Organisation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientSource extends Model
{
    protected $fillable = [
        'organisation_id',
        'period',
        'month',
        'north_america',
        'europe_asia',
        'africa',
        'asia',
        'middle_east',
        'south_america',
        'oceania',
    ];

    protected $casts = [
        'period' => 'integer',
        'month' => 'integer',
        'north_america' => 'integer',
        'europe_asia' => 'integer',
        'africa' => 'integer',
        'asia' => 'integer',
        'middle_east' => 'integer',
        'south_america' => 'integer',
        'oceania' => 'integer',
    ];

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }

    public function getTotalClientsAttribute(): int
    {
        return $this->north_america +
               $this->europe_asia +
               $this->africa +
               $this->asia +
               $this->middle_east +
               $this->south_america +
               $this->oceania;
    }
} 