<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Species;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;

class ApiSpeciesController extends Controller
{
    use ApiResponses;

    public function index()
    {
        $species = Species::all();
        return $this->ok('Species retrieved successfully', $species);
    }
}
