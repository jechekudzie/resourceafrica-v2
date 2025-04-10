<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class ControlMeasure extends Model
{
    use HasFactory,HasSlug;

    protected $guarded = [];

  
    /**
     * Get the problem animal controls using this control measure.
     */
    public function problemAnimalControls()
    {
        return $this->belongsToMany(ProblemAnimalControl::class, 'pac_control_measures')
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
