<?php

namespace App\Models;

use App\Models\HistoricalData\PoachingRecord;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class PoachingMethod extends Model
{
    use HasFactory,HasSlug;

    protected $guarded = [];

   
    public function poachingRecords()
    {
        return $this->hasMany(PoachingRecord::class);
    }

    public function getSlugOptions() : SlugOptions
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
