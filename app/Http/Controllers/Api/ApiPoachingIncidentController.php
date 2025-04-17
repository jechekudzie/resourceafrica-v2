<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OffenceType;
use App\Models\Organisation;
use App\Models\PoacherType;
use App\Models\Poacher;
use App\Models\PoachingIncident;
use App\Models\PoachingMethod;
use App\Models\PoachingReason;
use App\Models\Species;
use App\Traits\ApiResponses;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiPoachingIncidentController extends Controller
{
    use ApiResponses;

    /**
     * Display a listing of poaching incidents for an organisation.
     */
    public function index(Organisation $organisation, Request $request)
        {
            $perPage = $request->query('per_page', 15);
            $page = $request->query('page', 1);

            $poachingIncidents = PoachingIncident::where('organisation_id', $organisation->id)
                ->with(['species', 'methods', 'poachers'])
                ->orderBy('date', 'desc')
                ->paginate($perPage);

        return $this->ok('Poaching incidents retrieved successfully', $poachingIncidents);
    }

    /**
     * Display the specified poaching incident.
     */
    public function show(Organisation $organisation, PoachingIncident $poachingIncident)
    {
        if ($poachingIncident->organisation_id !== $organisation->id) {
            return $this->notFound('Poaching incident not found');
        }

        $poachingIncident->load(['species', 'methods', 'poachers']);

        return $this->ok('Poaching incident retrieved successfully', $poachingIncident);
    }

    /**
     * Store a newly created poaching incident.
     */
    public function store(Request $request, Organisation $organisation)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'location' => 'nullable|string',
            'longitude' => 'nullable|numeric',
            'latitude' => 'nullable|numeric',
            'period' => 'required|integer',
            'date' => 'required|date',
            'time' => 'required',
            'docket_number' => 'nullable|string|max:255',
            'docket_status' => 'nullable|string|in:open,under investigation,closed,pending court,convicted',
            'species' => 'required|array',
            'species.*.id' => 'required|exists:species,id',
            'species.*.estimate_number' => 'nullable|integer|min:0',
            'poaching_methods' => 'required|array',
            'poaching_methods.*' => 'exists:poaching_methods,id',
            'poachers' => 'required|array',
            'poachers.*.first_name' => 'required|string|max:255',
            'poachers.*.last_name' => 'required|string|max:255',
            'poachers.*.middle_name' => 'nullable|string|max:255',
            'poachers.*.age' => 'nullable|integer',
            'poachers.*.status' => 'nullable|string|in:suspected,arrested,bailed,sentenced,released',
            'poachers.*.country_id' => 'nullable|exists:countries,id',
            'poachers.*.province_id' => 'nullable|exists:provinces,id',
            'poachers.*.city_id' => 'nullable|exists:cities,id',
            'poachers.*.offence_type_id' => 'nullable|exists:offence_types,id',
            'poachers.*.poacher_type_id' => 'nullable|exists:poacher_types,id',
            'poachers.*.poaching_reason_id' => 'nullable|exists:poaching_reasons,id',
        ]);

        try {
            DB::beginTransaction();

            // Create poaching incident
            $poachingIncident = PoachingIncident::create([
                'organisation_id' => $organisation->id,
                'title' => $validated['title'],
                'location' => $validated['location'],
                'longitude' => $validated['longitude'],
                'latitude' => $validated['latitude'],
                'docket_number' => $validated['docket_number'],
                'docket_status' => $validated['docket_status'],
                'period' => $validated['period'],
                'date' => $validated['date'],
                'time' => $validated['time'],
            ]);

            // Attach species
            foreach ($validated['species'] as $speciesData) {
                $poachingIncident->species()->attach($speciesData['id'], [
                    'estimate_number' => $speciesData['estimate_number'] ?? null,
                ]);
            }

            // Attach poaching methods
            $poachingIncident->methods()->attach($validated['poaching_methods']);

            // Create poachers
            foreach ($validated['poachers'] as $poacherData) {
                $poachingIncident->poachers()->create([
                    'first_name' => $poacherData['first_name'],
                    'last_name' => $poacherData['last_name'],
                    'middle_name' => $poacherData['middle_name'] ?? null,
                    'age' => $poacherData['age'] ?? null,
                    'status' => $poacherData['status'] ?? null,
                    'country_id' => $poacherData['country_id'] ?? null,
                    'province_id' => $poacherData['province_id'] ?? null,
                    'city_id' => $poacherData['city_id'] ?? null,
                    'offence_type_id' => $poacherData['offence_type_id'] ?? null,
                    'poacher_type_id' => $poacherData['poacher_type_id'] ?? null,
                    'poaching_reason_id' => $poacherData['poaching_reason_id'] ?? null,
                ]);
            }

            DB::commit();

            // Load relationships for the response
            $poachingIncident->load(['species', 'methods', 'poachers']);

            return $this->created('Poaching incident created successfully', $poachingIncident);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Failed to create poaching incident: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update the specified poaching incident.
     */
    public function update(Request $request, Organisation $organisation, PoachingIncident $poachingIncident)
    {
        if ($poachingIncident->organisation_id !== $organisation->id) {
            return $this->notFound('Poaching incident not found');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'location' => 'nullable|string',
            'longitude' => 'nullable|numeric',
            'latitude' => 'nullable|numeric',
            'period' => 'required|integer',
            'date' => 'required|date',
            'time' => 'required',
            'docket_number' => 'nullable|string|max:255',
            'docket_status' => 'nullable|string|in:open,under investigation,closed,pending court,convicted',
            'species' => 'required|array',
            'species.*.id' => 'required|exists:species,id',
            'species.*.estimate_number' => 'nullable|integer|min:0',
            'poaching_methods' => 'required|array',
            'poaching_methods.*' => 'exists:poaching_methods,id',
            'poachers' => 'required|array',
            'poachers.*.id' => 'nullable|exists:poachers,id',
            'poachers.*.first_name' => 'required|string|max:255',
            'poachers.*.last_name' => 'required|string|max:255',
            'poachers.*.middle_name' => 'nullable|string|max:255',
            'poachers.*.age' => 'nullable|integer',
            'poachers.*.status' => 'nullable|string|in:suspected,arrested,bailed,sentenced,released',
            'poachers.*.country_id' => 'nullable|exists:countries,id',
            'poachers.*.province_id' => 'nullable|exists:provinces,id',
            'poachers.*.city_id' => 'nullable|exists:cities,id',
            'poachers.*.offence_type_id' => 'nullable|exists:offence_types,id',
            'poachers.*.poacher_type_id' => 'nullable|exists:poacher_types,id',
            'poachers.*.poaching_reason_id' => 'nullable|exists:poaching_reasons,id',
        ]);

        try {
            DB::beginTransaction();

            // Update poaching incident
            $poachingIncident->update([
                'title' => $validated['title'],
                'location' => $validated['location'],
                'longitude' => $validated['longitude'],
                'latitude' => $validated['latitude'],
                'docket_number' => $validated['docket_number'],
                'docket_status' => $validated['docket_status'],
                'period' => $validated['period'],
                'date' => $validated['date'],
                'time' => $validated['time'],
            ]);

            // Update species
            $poachingIncident->species()->detach();
            foreach ($validated['species'] as $speciesData) {
                $poachingIncident->species()->attach($speciesData['id'], [
                    'estimate_number' => $speciesData['estimate_number'] ?? null,
                ]);
            }

            // Update poaching methods
            $poachingIncident->methods()->sync($validated['poaching_methods']);

            // Process existing and new poachers
            $existingPoacherIds = [];
            foreach ($validated['poachers'] as $poacherData) {
                if (isset($poacherData['id']) && $poacherData['id']) {
                    // Update existing poacher
                    $poacher = Poacher::find($poacherData['id']);
                    if ($poacher && $poacher->poaching_incident_id == $poachingIncident->id) {
                        $poacher->update([
                            'first_name' => $poacherData['first_name'],
                            'last_name' => $poacherData['last_name'],
                            'middle_name' => $poacherData['middle_name'] ?? null,
                            'age' => $poacherData['age'] ?? null,
                            'status' => $poacherData['status'] ?? null,
                            'country_id' => $poacherData['country_id'] ?? null,
                            'province_id' => $poacherData['province_id'] ?? null,
                            'city_id' => $poacherData['city_id'] ?? null,
                            'offence_type_id' => $poacherData['offence_type_id'] ?? null,
                            'poacher_type_id' => $poacherData['poacher_type_id'] ?? null,
                            'poaching_reason_id' => $poacherData['poaching_reason_id'] ?? null,
                        ]);
                        $existingPoacherIds[] = $poacher->id;
                    } else {
                        // If poacher doesn't exist or doesn't belong to this incident, create a new one
                        $poacher = $poachingIncident->poachers()->create([
                            'first_name' => $poacherData['first_name'],
                            'last_name' => $poacherData['last_name'],
                            'middle_name' => $poacherData['middle_name'] ?? null,
                            'age' => $poacherData['age'] ?? null,
                            'status' => $poacherData['status'] ?? null,
                            'country_id' => $poacherData['country_id'] ?? null,
                            'province_id' => $poacherData['province_id'] ?? null,
                            'city_id' => $poacherData['city_id'] ?? null,
                            'offence_type_id' => $poacherData['offence_type_id'] ?? null,
                            'poacher_type_id' => $poacherData['poacher_type_id'] ?? null,
                            'poaching_reason_id' => $poacherData['poaching_reason_id'] ?? null,
                        ]);
                        $existingPoacherIds[] = $poacher->id;
                    }
                } else {
                    // Create new poacher
                    $poacher = $poachingIncident->poachers()->create([
                        'first_name' => $poacherData['first_name'],
                        'last_name' => $poacherData['last_name'],
                        'middle_name' => $poacherData['middle_name'] ?? null,
                        'age' => $poacherData['age'] ?? null,
                        'status' => $poacherData['status'] ?? null,
                        'country_id' => $poacherData['country_id'] ?? null,
                        'province_id' => $poacherData['province_id'] ?? null,
                        'city_id' => $poacherData['city_id'] ?? null,
                        'offence_type_id' => $poacherData['offence_type_id'] ?? null,
                        'poacher_type_id' => $poacherData['poacher_type_id'] ?? null,
                        'poaching_reason_id' => $poacherData['poaching_reason_id'] ?? null,
                    ]);
                    $existingPoacherIds[] = $poacher->id;
                }
            }

            // Delete poachers that were removed
            $poachingIncident->poachers()
                ->whereNotIn('id', $existingPoacherIds)
                ->delete();

            DB::commit();

            // Load relationships for the response
            $poachingIncident->load(['species', 'methods', 'poachers']);

            return $this->ok('Poaching incident updated successfully', $poachingIncident);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Failed to update poaching incident: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified poaching incident.
     */
    public function destroy(Organisation $organisation, PoachingIncident $poachingIncident)
    {
        if ($poachingIncident->organisation_id !== $organisation->id) {
            return $this->notFound('Poaching incident not found');
        }

        try {
            DB::beginTransaction();

            // Delete associated poachers
            $poachingIncident->poachers()->delete();

            // Delete the poaching incident
            $poachingIncident->delete();

            DB::commit();

            return $this->ok('Poaching incident deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Failed to delete poaching incident: ' . $e->getMessage(), 500);
        }
    }
}
