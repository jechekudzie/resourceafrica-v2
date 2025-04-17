<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ConflictType;
use App\Models\ControlMeasure;
use App\Models\Organisation;
use App\Models\ProblemAnimalControl;
use App\Models\WildlifeConflictIncident;
use App\Traits\ApiResponses;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ApiWildlifeConflictController extends Controller
{
    use ApiResponses;

    public function index(Organisation $organisation, Request $request)
        {
            $perPage = $request->query('per_page', 15);
            $page = $request->query('page', 1);

            $wildlifeConflictIncidents = WildlifeConflictIncident::where('organisation_id', $organisation->id)
                ->with(['species', 'conflictType', 'outcomes'])
                ->orderBy('incident_date', 'desc')
                ->paginate($perPage);

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

    public function controlMeasures($id)
    {
        $controlMeasures = ControlMeasure::where('conflict_type_id', '!=', $id)
            ->with(['problemAnimalControls.wildlifeConflictIncident'])
            ->orderBy('name')
            ->get();
        return $this->ok('Control Measures retrieved successfully', $controlMeasures);
    }

    public function conflictTypes()
    {
        $conflictTypes = ConflictType::all();
        return $this->ok('Conflict Types retrieved successfully', $conflictTypes);
    }

    /**
     * Display the specified incident.
     */
    public function show(Organisation $organisation, WildlifeConflictIncident $wildlifeConflictIncident)
    {
        if ($wildlifeConflictIncident->organisation_id !== $organisation->id) {
            return $this->notFound('Wildlife conflict incident not found');
        }

        $wildlifeConflictIncident->load([
            'species',
            'conflictType',
            'outcomes.conflictOutCome',
            'outcomes.dynamicValues.dynamicField',
            'problemAnimalControls.controlMeasures'
        ]);

        return $this->ok('Wildlife conflict incident retrieved successfully', $wildlifeConflictIncident);
    }

    /**
     * Store a newly created incident in storage.
     */
    public function store(Request $request, Organisation $organisation)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'period' => 'required|integer|min:1900|max:' . date('Y'),
            'incident_date' => 'required|date_format:Y-m-d',
            'incident_time' => 'required|date_format:H:i',
            'longitude' => 'nullable|numeric|between:-180,180',
            'latitude' => 'nullable|numeric|between:-90,90',
            'location_description' => 'required|string|max:255',
            'description' => 'required|string',
            'conflict_type_id' => 'required|exists:conflict_types,id',
            'species' => 'required|array|min:1',
            'species.*' => 'exists:species,id'
        ]);

        $wildlifeConflictIncident = WildlifeConflictIncident::create([
            'organisation_id' => $organisation->id,
            'title' => $validated['title'],
            'period' => $validated['period'],
            'incident_date' => Carbon::parse($validated['incident_date'])->format('Y-m-d'),
            'incident_time' => $validated['incident_time'],
            'longitude' => $validated['longitude'],
            'latitude' => $validated['latitude'],
            'location_description' => $validated['location_description'],
            'description' => $validated['description'],
            'conflict_type_id' => $validated['conflict_type_id']
        ]);

        $wildlifeConflictIncident->species()->attach($validated['species']);

        // Load relationships for the response
        $wildlifeConflictIncident->load(['species', 'conflictType']);

        return $this->created('Wildlife conflict incident has been recorded successfully', $wildlifeConflictIncident);
    }

    /**
     * Update the specified incident in storage.
     */
    public function update(Request $request, Organisation $organisation, WildlifeConflictIncident $wildlifeConflictIncident)
    {
        if ($wildlifeConflictIncident->organisation_id !== $organisation->id) {
            return $this->notFound('Wildlife conflict incident not found');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'period' => 'required|integer|min:1900|max:' . date('Y'),
            'incident_date' => 'required|date_format:Y-m-d',
            'incident_time' => 'required|date_format:H:i',
            'longitude' => 'nullable|numeric|between:-180,180',
            'latitude' => 'nullable|numeric|between:-90,90',
            'location_description' => 'required|string|max:255',
            'description' => 'required|string',
            'conflict_type_id' => 'required|exists:conflict_types,id',
            'species' => 'required|array|min:1',
            'species.*' => 'exists:species,id'
        ]);

        $wildlifeConflictIncident->update([
            'title' => $validated['title'],
            'period' => $validated['period'],
            'incident_date' => Carbon::parse($validated['incident_date'])->format('Y-m-d'),
            'incident_time' => $validated['incident_time'],
            'longitude' => $validated['longitude'],
            'latitude' => $validated['latitude'],
            'location_description' => $validated['location_description'],
            'description' => $validated['description'],
            'conflict_type_id' => $validated['conflict_type_id']
        ]);

        $wildlifeConflictIncident->species()->sync($validated['species']);

        // Load relationships for the response
        $wildlifeConflictIncident->load(['species', 'conflictType']);

        return $this->ok('Wildlife conflict incident has been updated successfully', $wildlifeConflictIncident);
    }

    /**
     * Remove the specified incident from storage.
     */
    public function destroy(Organisation $organisation, WildlifeConflictIncident $wildlifeConflictIncident)
    {
        if ($wildlifeConflictIncident->organisation_id !== $organisation->id) {
            return $this->notFound('Wildlife conflict incident not found');
        }

        $wildlifeConflictIncident->delete();

        return $this->ok('Wildlife conflict incident has been deleted successfully');
    }
}