<?php

namespace App\Models;

use App\Models\Organisation;
use App\Models\PayableItem;
use App\Models\TransactionPayable;
use App\Models\Species;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganisationPayableItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    public function payableItem()
    {
        return $this->belongsTo(PayableItem::class);
    }

    public function transactionPayables()
    {
        return $this->hasMany(TransactionPayable::class);
    }

    public function species()
    {
        return $this->belongsToMany(Species::class, 'organisation_payable_item_species', 'organisation_payable_item_id', 'species_id')
            ->withPivot('amount')
            ->withTimestamps();
    }
}
