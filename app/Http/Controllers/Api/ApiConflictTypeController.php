<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ConflictType;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;

class ApiConflictTypeController extends Controller
{
    use ApiResponses;

    public function index()
    {
        $types = ConflictType::all();
        return $this->ok('Conflict types retrieved successfully', $types);
    }
}
