<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ConflictType;
use App\Models\ControlMeasure;
use App\Models\Organisation;
use App\Models\ProblemAnimalControl;
use App\Models\WildlifeConflictIncident;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;

class ApiWildlifeConflictController extends Controller
{
    use ApiResponses;

    public function index(Organisation $organisation)
    {
        $wildlifeConflictIncidents = WildlifeConflictIncident::where('organisation_id', $organisation->id)
            ->with(['species', 'conflictType'])
            ->orderBy('incident_date', 'desc')->get();
        return $this->ok('Wildlife Conflict Incidents retrieved successfully', $wildlifeConflictIncidents);
    }

    public function problemAnimalControls(Organisation $organisation)
    {
        $problemAnimalControls = ProblemAnimalControl::where('organisation_id', $organisation->id)
            ->with(['wildlifeConflictIncident', 'controlMeasures'])
            ->orderBy('control_date', 'desc')
            ->get();

        return $this->ok('Problem Animal Controls retrieved successfully', $problemAnimalControls);
    }

    public function controlMeasures()
    {
        $controlMeasures = ControlMeasure::all();
        return $this->ok('Control Measures retrieved successfully', $controlMeasures);
    }

    public function conflictTypes()
    {
        $conflictTypes = ConflictType::all();
        return $this->ok('Conflict Types retrieved successfully', $conflictTypes);
    }
}
