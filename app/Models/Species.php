<?php

namespace App\Models;

use App\Models\Organisation;
use App\Models\ControlCase;
use App\Models\HuntingDetail;
use App\Models\OrganisationPayableItem;
use App\Models\TransactionPayable;
use App\Models\QuotaAllocation;
use App\Models\HuntingActivity;
use App\Models\Incident;
use App\Models\PACDetail;
use App\Models\PopulationEstimate;
use App\Models\PoachingIncident;
use App\Models\QuotaRequest;
use App\Models\HistoricalData\AnimalControlRecord;
use App\Models\HistoricalData\ConflictRecord;
use App\Models\HistoricalData\CropConflictRecord;
use App\Models\HistoricalData\HumanConflictRecord;
use App\Models\HistoricalData\HuntingRecord;
use App\Models\HistoricalData\LiveStockConflictRecord;
use App\Models\HistoricalData\PoachersRecord;
use App\Models\HistoricalData\PoachingRecord;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Species extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'name',
        'scientific_name',
        'description',
        'category',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

  

    public function getEstimateCount($maturityId, $genderId, $year)
    {
        return $this->populationEstimates()
            ->where('maturity_id', $maturityId)
            ->where('species_gender_id', $genderId)
            ->where('year', $year)
            ->sum('estimate');
    }


    // Species-specific pricing for Payable Items within Organisations
    public function organisationPayableItems()
    {
        return $this->belongsToMany(OrganisationPayableItem::class, 'organisation_payable_item_species', 'species_id', 'organisation_payable_item_id')
            ->withPivot('amount')
            ->withTimestamps();
    }

    public function transactionPayables()
    {
        return $this->belongsToMany(TransactionPayable::class, 'transaction_payable_species', 'species_id', 'transaction_payable_id')
            ->withPivot('amount')
            ->withTimestamps();
    }



    public function huntingRecords()
    {
        return $this->hasMany(HuntingRecord::class);
    }

    public function conflictRecords()
    {
        return $this->hasMany(ConflictRecord::class);
    }

    public function cropConflictRecords()
    {
        return $this->hasMany(CropConflictRecord::class);
    }

    public function liveStockConflictRecords()
    {
        return $this->hasMany(LiveStockConflictRecord::class);
    }

    public function humanConflictRecords()
    {
        return $this->hasMany(HumanConflictRecord::class);
    }

    public function animalControlRecords()
    {
        return $this->hasMany(AnimalControlRecord::class);
    }

    public function poachingRecords()
    {
        return $this->hasMany(PoachingRecord::class);
    }

    public function poachersRecords()
    {
        return $this->hasMany(PoachersRecord::class);
    }

    public function controlCases()
    {
        return $this->hasMany(ControlCase::class);
    }

    public function quotaAllocations()
    {
        return $this->hasMany(QuotaAllocation::class);
    }

    public function huntingActivities()
    {
        return $this->belongsToMany(HuntingActivity::class, 'hunting_activity_species')
            ->withPivot('off_take')
            ->withTimestamps();
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }


}
