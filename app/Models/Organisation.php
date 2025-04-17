<?php

namespace App\Models;

use App\Models\Hunter;
use App\Models\HuntingActivity;
use App\Models\HuntingLicense;
use App\Models\Incident;
use App\Models\OrganisationType;
use App\Models\PoachingIncident;
use App\Models\PopulationEstimate;
use App\Models\ProblemAnimalControl;
use App\Models\QuotaRequest;
use App\Models\RevenueSharing;
use App\Models\WardQuotaDistribution;
use App\Models\HistoricalData\AnimalControlRecord;
use App\Models\HistoricalData\ConflictRecord;
use App\Models\HistoricalData\CropConflictRecord;
use App\Models\HistoricalData\HumanConflictRecord;
use App\Models\HistoricalData\HuntingRecord;
use App\Models\HistoricalData\IncomeRecord;
use App\Models\HistoricalData\LiveStockConflictRecord;
use App\Models\HistoricalData\PoachersRecord;
use App\Models\HistoricalData\PoachingRecord;
use App\Models\HuntingConcession;
use App\Models\OrganisationPayableItem;
use App\Models\QuotaAllocation;
use App\Models\Transaction;
use App\Models\User;
use App\Models\IncomeSource;
use App\Models\IncomeUsage;
use App\Models\ClientSource;
use App\Models\ControlCase;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organisation extends Model
{
    use HasFactory, HasSlug;

    protected $guarded = [];


    public function organisationType()
    {
        return $this->belongsTo(OrganisationType::class);
    }

    //has many organisations
    public function organisations()
    {
        return $this->hasMany(Organisation::class);
    }


    public function parentOrganisation()
    {
        return $this->belongsTo(Organisation::class, 'organisation_id');
    }

    public function childOrganisations()
    {
        return $this->hasMany(Organisation::class, 'organisation_id');
    }

    public function getSafariOperators()
    {
        return $this->childOrganisations()
            ->whereHas('organisationType', function ($query) {
                $query->whereRaw('lower(name) = ?', ['safari operators']);
            })->get();
    }

    public function safariOperators()
    {

        $safariOperators = $this->childOrganisations()->whereHas('organisationType', function ($query) {
            $query->whereRaw('lower(name) = ?', ['safari operators']);
        });

        return $safariOperators;

    }


    //get the first group of child organisations
    public function firstGroupOfChildOrganisations()
    {
        // Get all child organizations
        $childOrganizations = $this->hasMany(Organisation::class, 'organisation_id')->get();

        // Group the child organizations by organisation_type_id
        $groupedOrganizations = $childOrganizations->groupBy('organisation_type_id');

        // Get the first group
        $firstGroup = $groupedOrganizations->skip(1)->first();

        return $firstGroup;
    }

    //get any group of child organisations
    public function anyGroupOfChildOrganisations()
    {
        // Get all child organizations
        $childOrganizations = $this->hasMany(Organisation::class, 'organisation_id')->get();

        // Group the child organizations by organisation_type_id
        $groupedOrganizations = $childOrganizations->groupBy('organisation_type_id');

        // Get the second group using skip(1) or any other index
        $anyGroup = $groupedOrganizations->skip(1)->first();

        return $anyGroup;
    }

    //get all children
    public function getAllChildren()
    {
        $children = [];

        foreach ($this->childOrganisations as $child) {
            $children[] = $child;
            $children = array_merge($children, $child->getAllChildren());
        }

        return $children;
    }

    //get all hunting concessions
    public function huntingConcessions() // When the organisation is an RDC
    {
        return $this->hasMany(HuntingConcession::class);
    }

    public function huntingActivities()
    {
        return $this->hasMany(HuntingActivity::class);
    }

    public function organisationPayableItems()
    {
        return $this->hasMany(OrganisationPayableItem::class);
    }

    // Add this method to define the relationship with Transactions
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    //hunting records
    public function huntingRecords()
    {
        return $this->hasMany(HuntingRecord::class);
    }

    public function conflictRecords()
    {
        return $this->hasMany(ConflictRecord::class);
    }

    //for historical data
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

    public function quotaAllocations()
    {
        return $this->hasMany(QuotaAllocation::class);
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

    //has many income records
    public function incomeRecords()
    {
        return $this->hasMany(IncomeRecord::class);
    }

    /**
     * Get all income sources for this organisation
     */
    public function incomeSources()
    {
        return $this->hasMany(IncomeSource::class);
    }

    /**
     * Get all income usages for this organisation
     */
    public function incomeUsages()
    {
        return $this->hasMany(IncomeUsage::class);
    }

    //belongs to many users
    public function users()
    {
        return $this->belongsToMany(User::class, 'organisation_users')
            ->withPivot('role_id');
    }

    //has many roles
    public function organisationRoles()
    {
        return $this->hasMany(\Spatie\Permission\Models\Role::class, 'organisation_id');
    }

    public function availablePermissions()
    {
        return $this->belongsToMany(\Spatie\Permission\Models\Permission::class, 'organisation_permissions');
    }

    public function clientSources(): HasMany
    {
        return $this->hasMany(ClientSource::class);
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
