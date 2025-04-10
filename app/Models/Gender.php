<?php

namespace App\Models;

use App\Models\HistoricalData\HumanConflictRecord;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Gender extends Model
{
    use HasFactory,HasSlug;

    protected $guarded = [];

    public function poachers()
    {
        return $this->hasMany(Poacher::class);
    }

    public function humanConflictRecords()
    {
        return $this->hasMany(HumanConflictRecord::class);
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
