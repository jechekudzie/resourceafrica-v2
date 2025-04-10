<?php

namespace App\Models;

use App\Models\Organisation;
use App\Models\HuntingActivity;
use App\Models\TransactionPayable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $guarded = [];
    
    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }
    
    public function payables()
    {
        return $this->hasMany(TransactionPayable::class);
    }
    
    public function huntingActivities()
    {
        return $this->hasMany(HuntingActivity::class);
    }
}
