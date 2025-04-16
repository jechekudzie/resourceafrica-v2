<?php

namespace App\Http\Controllers\Organisation;

use App\Http\Controllers\Controller;
use App\Models\ConflictOutcome;
use App\Models\Organisation;
use App\Models\DynamicField;
use App\Models\WildlifeConflictIncident;
use App\Models\WildlifeConflictOutcome;
use App\Models\WildlifeConflictDynamicValue;
use Illuminate\Http\Request;

class WildlifeConflictOutcomeController extends Controller
{
    /**
     * Show the form for creating a new outcome.
     */
    public function create(Organisation $organisation, WildlifeConflictIncident $wildlifeConflictIncident)
    {
        // Get all available conflict outcomes
        $conflictOutcomes = ConflictOutcome::all();
        
        // Remove the filtering of existing outcomes
        // $existingOutcomeIds = $wildlifeConflictIncident->outcomes->pluck('conflict_outcome_id')->toArray();
        
        return view('organisation.wildlife-conflicts.outcomes.create', [
            'organisation' => $organisation,
            'wildlifeConflictIncident' => $wildlifeConflictIncident,
            'conflictOutcomes' => $conflictOutcomes,
            // Remove the existingOutcomeIds from the view data
        ]);
    }

    /**
     * Store a newly created outcome in storage.
     */
    public function store(Request $request, Organisation $organisation, WildlifeConflictIncident $wildlifeConflictIncident)
    {
        $validated = $request->validate([
            'conflict_outcome_id' => 'required|exists:conflict_outcomes,id',
        ]);

        // Create the outcome
        $outcome = WildlifeConflictOutcome::create([
            'wildlife_conflict_incident_id' => $wildlifeConflictIncident->id,
            'conflict_outcome_id' => $validated['conflict_outcome_id'],
        ]);

        // Get dynamic fields for this outcome type, filtered by organization
        $dynamicFields = DynamicField::where('conflict_outcome_id', $validated['conflict_outcome_id'])
            ->where('organisation_id', $organisation->id)
            ->get();
        
        // Process dynamic field values if any
        foreach ($dynamicFields as $field) {
            $fieldName = 'dynamic_field_' . $field->id;
            if ($request->has($fieldName)) {
                WildlifeConflictDynamicValue::create([
                    'wildlife_conflict_outcome_id' => $outcome->id,
                    'dynamic_field_id' => $field->id,
                    'field_value' => $request->input($fieldName),
                ]);
            }
        }

        return redirect()->route('organisation.wildlife-conflicts.show', [
            'organisation' => $organisation->slug,
            'wildlifeConflictIncident' => $wildlifeConflictIncident->id
        ])->with('success', 'Conflict outcome added successfully.');
    }

    /**
     * Display the specified outcome.
     */
    public function show(Organisation $organisation, WildlifeConflictIncident $wildlifeConflictIncident, WildlifeConflictOutcome $outcome)
    {
        // Load the outcome with its dynamic values and their associated fields
        $outcome->load(['conflictOutcome', 'dynamicValues.dynamicField']);
        
        return view('organisation.wildlife-conflicts.outcomes.show', [
            'organisation' => $organisation,
            'wildlifeConflictIncident' => $wildlifeConflictIncident,
            'outcome' => $outcome
        ]);
    }

    /**
     * Show the form for editing the specified outcome.
     */
    public function edit(Organisation $organisation, WildlifeConflictIncident $wildlifeConflictIncident, WildlifeConflictOutcome $outcome)
    {
        // Get dynamic fields for this outcome type, filtered by organization
        $dynamicFields = DynamicField::where('conflict_outcome_id', $outcome->conflict_outcome_id)
            ->where('organisation_id', $organisation->id)
            ->get();
        
        // Get existing values
        $dynamicValues = $outcome->dynamicValues->pluck('field_value', 'dynamic_field_id')->toArray();
        
        return view('organisation.wildlife-conflicts.outcomes.edit', [
            'organisation' => $organisation,
            'wildlifeConflictIncident' => $wildlifeConflictIncident,
            'outcome' => $outcome,
            'dynamicFields' => $dynamicFields,
            'dynamicValues' => $dynamicValues
        ]);
    }

    /**
     * Update the specified outcome in storage.
     */
    public function update(Request $request, Organisation $organisation, WildlifeConflictIncident $wildlifeConflictIncident, WildlifeConflictOutcome $outcome)
    {
        // Get dynamic fields for this outcome type, filtered by organization
        $dynamicFields = DynamicField::where('conflict_outcome_id', $outcome->conflict_outcome_id)
            ->where('organisation_id', $organisation->id)
            ->get();
        
        // Process dynamic field values
        foreach ($dynamicFields as $field) {
            $fieldName = 'dynamic_field_' . $field->id;
            if ($request->has($fieldName)) {
                $value = $request->input($fieldName);
                
                // Update or create the dynamic value
                WildlifeConflictDynamicValue::updateOrCreate(
                    [
                        'wildlife_conflict_outcome_id' => $outcome->id,
                        'dynamic_field_id' => $field->id,
                    ],
                    [
                        'field_value' => $value,
                    ]
                );
            }
        }

        return redirect()->route('organisation.wildlife-conflicts.show', [
            'organisation' => $organisation->slug,
            'wildlifeConflictIncident' => $wildlifeConflictIncident->id
        ])->with('success', 'Conflict outcome updated successfully.');
    }

    /**
     * Remove the specified outcome from storage.
     */
    public function destroy(Organisation $organisation, WildlifeConflictIncident $wildlifeConflictIncident, WildlifeConflictOutcome $outcome)
    {
        // Delete associated dynamic values first (should be handled by cascade, but just to be safe)
        $outcome->dynamicValues()->delete();
        
        // Delete the outcome
        $outcome->delete();

        return redirect()->route('organisation.wildlife-conflicts.show', [
            'organisation' => $organisation->slug,
            'wildlifeConflictIncident' => $wildlifeConflictIncident->id
        ])->with('success', 'Conflict outcome removed successfully.');
    }
} 