<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DynamicField;
use App\Models\ConflictOutcome;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ConflictOutcomeController extends Controller
{
    /**
     * Get dynamic fields for a specific conflict outcome.
     */
    public function getDynamicFields($conflictOutcome, $organisationId = null)
    {
        try {
            // Manual lookup instead of model binding
            $outcomeModel = ConflictOutcome::findOrFail($conflictOutcome);
            
            Log::info('Fetching dynamic fields for conflict outcome', [
                'outcome_id' => $outcomeModel->id,
                'outcome_name' => $outcomeModel->name,
                'organisation_id' => $organisationId
            ]);

            $query = DynamicField::where('conflict_outcome_id', $outcomeModel->id);
            
            // Filter by organisation if provided
            if ($organisationId) {
                $query->where('organisation_id', $organisationId);
            }
            
            $fields = $query->with('options')->get();
                
            Log::info('Found dynamic fields', [
                'count' => $fields->count(),
                'field_ids' => $fields->pluck('id')
            ]);
                
            $formattedFields = $fields->map(function ($field) {
                return [
                    'id' => $field->id,
                    'label' => $field->label,
                    'field_type' => $field->field_type,
                    'options' => $field->options->map(function ($option) {
                        return [
                            'value' => $option->option_value,
                            'label' => $option->option_label
                        ];
                    })
                ];
            });
            
            return response()->json($formattedFields);
        } catch (\Exception $e) {
            Log::error('Error fetching dynamic fields', [
                'outcome_id' => $conflictOutcome,
                'organisation_id' => $organisationId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['error' => 'Failed to load dynamic fields: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get all conflict outcomes with their related dynamic fields.
     */
    public function getAllOutcomes($organisationId = null)
    {
        try {
            Log::info('Fetching all conflict outcomes with dynamic fields', [
                'organisation_id' => $organisationId
            ]);

            $outcomes = ConflictOutcome::all();
            
            Log::info('Found conflict outcomes', [
                'count' => $outcomes->count(),
                'outcome_ids' => $outcomes->pluck('id')
            ]);
            
            $formattedOutcomes = $outcomes->map(function ($outcome) use ($organisationId) {
                // Filter dynamic fields by organisation
                $dynamicFieldsQuery = $outcome->dynamicFields();
                
                if ($organisationId) {
                    $dynamicFieldsQuery->where('organisation_id', $organisationId);
                }
                
                $dynamicFields = $dynamicFieldsQuery->with('options')->get();
                
                return [
                    'id' => $outcome->id,
                    'name' => $outcome->name,
                    'dynamic_fields' => $dynamicFields->map(function ($field) {
                        return [
                            'id' => $field->id,
                            'label' => $field->label,
                            'field_type' => $field->field_type,
                            'options' => $field->options->map(function ($option) {
                                return [
                                    'value' => $option->option_value,
                                    'label' => $option->option_label
                                ];
                            })
                        ];
                    })
                ];
            });
            
            return response()->json($formattedOutcomes);
        } catch (\Exception $e) {
            Log::error('Error fetching all conflict outcomes', [
                'organisation_id' => $organisationId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['error' => 'Failed to load conflict outcomes: ' . $e->getMessage()], 500);
        }
    }
} 