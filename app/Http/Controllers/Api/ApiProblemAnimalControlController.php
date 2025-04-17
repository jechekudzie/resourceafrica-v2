<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ControlMeasure;
use App\Models\Organisation;
use App\Models\ProblemAnimalControl;
use App\Models\WildlifeConflictIncident;
use App\Traits\ApiResponses;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ApiProblemAnimalControlController extends Controller
{
    use ApiResponses;

    /**
     * Display a listing of problem animal controls for an organisation.
     */
    public function index(Organisation $organisation, Request $request)
        {
            $perPage = $request->query('per_page', 15);
            $page = $request->query('page', 1);

            $problemAnimalControls = ProblemAnimalControl::where('organisation_id', $organisation->id)
                ->with(['wildlifeConflictIncident.species', 'controlMeasures'])
                ->orderBy('control_date', 'desc')
                ->paginate($perPage);

        return $this->ok('Problem animal controls retrieved successfully', $problemAnimalControls);
    }

    /**
     * Display the specified problem animal control.
     */
    public function show(Organisation $organisation, ProblemAnimalControl $problemAnimalControl)
    {
        if ($problemAnimalControl->organisation_id !== $organisation->id) {
            return $this->notFound('Problem animal control not found');
        }

        $problemAnimalControl->load([
            'wildlifeConflictIncident.species',
            'controlMeasures',
        ]);

        return $this->ok('Problem animal control retrieved successfully', $problemAnimalControl);
    }

    /**
     * Store a newly created problem animal control.
     */
    public function store(Request $request, Organisation $organisation)
    {
        $validated = $request->validate([
            'wildlife_conflict_incident_id' => 'required|exists:wildlife_conflict_incidents,id',
            'control_date' => 'required|date_format:Y-m-d',
            'control_time' => 'nullable|date_format:H:i',
            'period' => 'required|integer|min:1900|max:' . date('Y'),
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'estimated_number' => 'required|integer|min:1',
            'control_measures' => 'required|array|min:1',
            'control_measures.*' => 'exists:control_measures,id',
        ]);

        // Verify the wildlife conflict incident belongs to this organisation
        $wildlifeConflictIncident = WildlifeConflictIncident::where('id', $validated['wildlife_conflict_incident_id'])
            ->where('organisation_id', $organisation->id)
            ->first();

        if (!$wildlifeConflictIncident) {
            return $this->notFound('Wildlife conflict incident not found or does not belong to this organisation');
        }

        $problemAnimalControl = ProblemAnimalControl::create([
            'organisation_id' => $organisation->id,
            'wildlife_conflict_incident_id' => $validated['wildlife_conflict_incident_id'],
            'control_date' => Carbon::parse($validated['control_date'])->format('Y-m-d'),
            'control_time' => $validated['control_time'] ?? null,
            'period' => $validated['period'],
            'location' => $validated['location'],
            'description' => $validated['description'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'estimated_number' => $validated['estimated_number'],
        ]);

        $problemAnimalControl->controlMeasures()->attach($validated['control_measures']);

        // Load relationships for the response
        $problemAnimalControl->load(['wildlifeConflictIncident.species', 'controlMeasures']);

        return $this->created('Problem animal control record has been created successfully', $problemAnimalControl);
    }

    /**
     * Update the specified problem animal control.
     */
    public function update(Request $request, Organisation $organisation, ProblemAnimalControl $problemAnimalControl)
    {
        if ($problemAnimalControl->organisation_id !== $organisation->id) {
            return $this->notFound('Problem animal control not found');
        }

        $validated = $request->validate([
            'wildlife_conflict_incident_id' => 'required|exists:wildlife_conflict_incidents,id',
            'control_date' => 'required|date_format:Y-m-d',
            'control_time' => 'nullable|date_format:H:i',
            'period' => 'required|integer|min:1900|max:' . date('Y'),
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'estimated_number' => 'required|integer|min:1',
            'control_measures' => 'required|array|min:1',
            'control_measures.*' => 'exists:control_measures,id',
        ]);

        // Verify the wildlife conflict incident belongs to this organisation
        $wildlifeConflictIncident = WildlifeConflictIncident::where('id', $validated['wildlife_conflict_incident_id'])
            ->where('organisation_id', $organisation->id)
            ->first();

        if (!$wildlifeConflictIncident) {
            return $this->notFound('Wildlife conflict incident not found or does not belong to this organisation');
        }

        $problemAnimalControl->update([
            'wildlife_conflict_incident_id' => $validated['wildlife_conflict_incident_id'],
            'control_date' => Carbon::parse($validated['control_date'])->format('Y-m-d'),
            'control_time' => $validated['control_time'] ?? null,
            'period' => $validated['period'],
            'location' => $validated['location'],
            'description' => $validated['description'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'estimated_number' => $validated['estimated_number'],
        ]);

        $problemAnimalControl->controlMeasures()->sync($validated['control_measures']);

        // Load relationships for the response
        $problemAnimalControl->load(['wildlifeConflictIncident.species', 'controlMeasures']);

        return $this->ok('Problem animal control record has been updated successfully', $problemAnimalControl);
    }

    /**
     * Remove the specified problem animal control.
     */
    public function destroy(Organisation $organisation, ProblemAnimalControl $problemAnimalControl)
    {
        if ($problemAnimalControl->organisation_id !== $organisation->id) {
            return $this->notFound('Problem animal control not found');
        }

        $problemAnimalControl->delete();

        return $this->ok('Problem animal control record has been deleted successfully');
    }
}
