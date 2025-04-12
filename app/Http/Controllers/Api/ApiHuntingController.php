<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HuntingConcession;
use App\Models\Organisation;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;

class ApiHuntingController extends Controller
{
    use ApiResponses;

    public function huntingConcessions()
    {
        $concessions = HuntingConcession::all();
        return $this->ok('Hunting Concessions retrieved successfully', $concessions);
    }

    public function safariOperators(Organisation $organisation)
    {
        $safaris = $organisation->getSafariOperators();
        return $this->ok('Hunting Concessions retrieved successfully', $safaris);
    }
}
