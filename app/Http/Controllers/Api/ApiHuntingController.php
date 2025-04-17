<?php

namespace App\Http\Controllers\Api;

use App\Models\Organisation;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use App\Models\HuntingActivity;
use App\Models\HuntingConcession;
use App\Models\Species;
use App\Models\QuotaAllocation;
use App\Models\QuotaAllocationBalance;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ApiHuntingController extends Controller
{
    use ApiResponses;

    public function huntingConcessions(Organisation $organisation)
    {
        $concessions = HuntingConcession::where('organisation_id', $organisation->id)->get();
        return $this->ok('Hunting Concessions retrieved successfully', $concessions);
    }

    public function huntingActivities(Organisation $organisation)
    {
        $activities = $organisation->huntingActivities()
            ->with(['huntingConcession', 'species', 'professionalHunterLicenses'])
            ->get();
        return $this->ok('Hunting Activities retrieved successfully', $activities);
    }

    public function showHuntingActivity(Organisation $organisation, HuntingActivity $huntingActivity)
    {
        $huntingActivity->load(['huntingConcession', 'species', 'professionalHunterLicenses']);
        return $this->ok('Hunting Activity retrieved successfully', $huntingActivity);
    }

    public function storeHuntingActivity(Request $request, Organisation $organisation)
    {
        $request->validate([
            'hunting_concession_id' => 'required|exists:hunting_concessions,id',
            'safari_id' => 'required|exists:organisations,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'period' => 'required|integer',
            'professional_hunter_licenses' => 'required|array',
            'professional_hunter_licenses.*.license_number' => 'required|string',
            'professional_hunter_licenses.*.hunter_name' => 'required|string',
            'species' => 'required|array',
            'species.*.id' => 'required|exists:species,id',
            'species.*.off_take' => 'required|integer|min:0'
        ]);

        try {
            DB::beginTransaction();

            // First validate all species quotas before creating anything
            $speciesData = collect($request->species)->groupBy('id')->map(function($items) {
                return [
                    'id' => $items->first()['id'],
                    'off_take' => $items->sum('off_take')
                ];
            })->values();

            foreach ($speciesData as $species) {
                // Check quota allocation exists and has sufficient balance
                $quotaAllocation = QuotaAllocation::where('organisation_id', $organisation->id)
                    ->where('species_id', $species['id'])
                    ->where('period', $request->period)
                    ->first();

                if (!$quotaAllocation) {
                    return $this->error("No quota allocation found for species ID {$species['id']} in period {$request->period}", 422);
                }

                // Get existing quota balance or calculate from quota allocation
                $quotaBalance = QuotaAllocationBalance::where('quota_allocation_id', $quotaAllocation->id)->first();

                if ($quotaBalance) {
                    $availableQuota = $quotaBalance->remaining_quota;
                } else {
                    $availableQuota = $quotaAllocation->hunting_quota;
                }

                if ($species['off_take'] > $availableQuota) {
                    $speciesName = Species::find($species['id'])->name;
                    return $this->error("Insufficient quota for {$speciesName}. Available: {$availableQuota}, Requested: {$species['off_take']}", 422);
                }
            }

            // Create the hunting activity
            $huntingActivity = $organisation->huntingActivities()->create([
                'hunting_concession_id' => $request->hunting_concession_id,
                'safari_id' => $request->safari_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'period' => $request->period
            ]);

            // Add professional hunter licenses
            foreach ($request->professional_hunter_licenses as $license) {
                $huntingActivity->professionalHunterLicenses()->create([
                    'license_number' => $license['license_number'],
                    'hunter_name' => $license['hunter_name']
                ]);
            }

            // Process species and update quota balances
            foreach ($speciesData as $species) {
                $quotaAllocation = QuotaAllocation::where('organisation_id', $organisation->id)
                    ->where('species_id', $species['id'])
                    ->where('period', $request->period)
                    ->first();

                // Get or create quota balance for this quota allocation
                $quotaBalance = QuotaAllocationBalance::where('quota_allocation_id', $quotaAllocation->id)->first();

                if (!$quotaBalance) {
                    $quotaBalance = QuotaAllocationBalance::create([
                        'quota_allocation_id' => $quotaAllocation->id,
                        'allocated_quota' => $quotaAllocation->hunting_quota,
                        'total_off_take' => 0,
                        'remaining_quota' => $quotaAllocation->hunting_quota
                    ]);
                }

                // Update the balance with new off-take value
                $newTotalOffTake = $quotaBalance->total_off_take + $species['off_take'];
                $newRemainingQuota = $quotaAllocation->hunting_quota - $newTotalOffTake;

                $quotaBalance->update([
                    'total_off_take' => $newTotalOffTake,
                    'remaining_quota' => $newRemainingQuota
                ]);

                // Attach species to hunting activity
                $huntingActivity->species()->attach($species['id'], [
                    'off_take' => $species['off_take']
                ]);
            }

            DB::commit();

            $huntingActivity->load(['huntingConcession', 'species', 'professionalHunterLicenses']);
            return $this->created('Hunting activity created successfully', $huntingActivity);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Failed to create hunting activity: ' . $e->getMessage(), 500);
        }
    }

    public function updateHuntingActivity(Request $request, Organisation $organisation, HuntingActivity $huntingActivity)
    {
        $request->validate([
            'hunting_concession_id' => 'required|exists:hunting_concessions,id',
            'safari_id' => 'required|exists:organisations,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'period' => 'required|integer',
            'professional_hunter_licenses' => 'required|array',
            'professional_hunter_licenses.*.license_number' => 'required|string',
            'professional_hunter_licenses.*.hunter_name' => 'required|string',
            'species' => 'required|array',
            'species.*.id' => 'required|exists:species,id',
            'species.*.off_take' => 'required|integer|min:0'
        ]);

        try {
            DB::beginTransaction();

            // Get the old species data for updating quota balances
            $oldSpeciesData = $huntingActivity->species->mapWithKeys(function ($species) {
                return [$species->id => $species->pivot->off_take];
            })->toArray();

            // Update the hunting activity
            $huntingActivity->update([
                'hunting_concession_id' => $request->hunting_concession_id,
                'safari_id' => $request->safari_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'period' => $request->period
            ]);

            // Update professional hunter licenses
            $huntingActivity->professionalHunterLicenses()->delete();
            foreach ($request->professional_hunter_licenses as $license) {
                $huntingActivity->professionalHunterLicenses()->create([
                    'license_number' => $license['license_number'],
                    'hunter_name' => $license['hunter_name']
                ]);
            }

            // Group and process new species data
            $newSpeciesData = collect($request->species)->groupBy('id')->map(function($items) {
                return [
                    'id' => $items->first()['id'],
                    'off_take' => $items->sum('off_take')
                ];
            })->keyBy('id')->toArray();

            // Detach all current species
            $huntingActivity->species()->detach();

            // Process species and update quota balances
            foreach ($newSpeciesData as $speciesId => $speciesData) {
                $quotaAllocation = QuotaAllocation::where('organisation_id', $organisation->id)
                    ->where('species_id', $speciesId)
                    ->where('period', $request->period)
                    ->first();

                if (!$quotaAllocation) {
                    return $this->error("No quota allocation found for species ID {$speciesId} in period {$request->period}", 422);
                }

                // Get the existing quota balance record
                $quotaBalance = QuotaAllocationBalance::where('quota_allocation_id', $quotaAllocation->id)->first();

                if (!$quotaBalance) {
                    $quotaBalance = QuotaAllocationBalance::create([
                        'quota_allocation_id' => $quotaAllocation->id,
                        'allocated_quota' => $quotaAllocation->hunting_quota,
                        'total_off_take' => 0,
                        'remaining_quota' => $quotaAllocation->hunting_quota
                    ]);
                } else {
                    // Adjust for the old off-take if this species was previously attached
                    $oldOffTake = $oldSpeciesData[$speciesId] ?? 0;
                    $quotaBalance->total_off_take -= $oldOffTake;
                }

                // Update with new off-take
                $newTotalOffTake = $quotaBalance->total_off_take + $speciesData['off_take'];
                $newRemainingQuota = $quotaAllocation->hunting_quota - $newTotalOffTake;

                $quotaBalance->update([
                    'total_off_take' => $newTotalOffTake,
                    'remaining_quota' => $newRemainingQuota
                ]);

                // Attach species with new off-take
                $huntingActivity->species()->attach($speciesId, [
                    'off_take' => $speciesData['off_take']
                ]);
            }

            DB::commit();

            $huntingActivity->load(['huntingConcession', 'species', 'professionalHunterLicenses']);
            return $this->ok('Hunting activity updated successfully', $huntingActivity);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Failed to update hunting activity: ' . $e->getMessage(), 500);
        }
    }

    public function destroyHuntingActivity(Organisation $organisation, HuntingActivity $huntingActivity)
    {
        try {
            DB::beginTransaction();

            // Get the old species data for updating quota balances
            $oldSpeciesData = $huntingActivity->species->mapWithKeys(function ($species) {
                return [$species->id => $species->pivot->off_take];
            })->toArray();

            // Update quota balances
            foreach ($oldSpeciesData as $speciesId => $offTake) {
                $quotaAllocation = QuotaAllocation::where('organisation_id', $organisation->id)
                    ->where('species_id', $speciesId)
                    ->where('period', $huntingActivity->period)
                    ->first();

                if ($quotaAllocation) {
                    $quotaBalance = QuotaAllocationBalance::where('quota_allocation_id', $quotaAllocation->id)->first();
                    if ($quotaBalance) {
                        $quotaBalance->update([
                            'total_off_take' => $quotaBalance->total_off_take - $offTake,
                            'remaining_quota' => $quotaBalance->remaining_quota + $offTake
                        ]);
                    }
                }
            }

            // Delete the hunting activity
            $huntingActivity->delete();

            DB::commit();
            return $this->ok('Hunting activity deleted successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Failed to delete hunting activity: ' . $e->getMessage(), 500);
        }
    }

    public function safariOperators(Organisation $organisation)
    {
        $safaris = $organisation->getSafariOperators();
        return $this->ok('Safari Operators retrieved successfully', $safaris);
    }

    public function huntingQuotas(Organisation $organisation)
    {
        $quotaAllocations = QuotaAllocation::with(['organisation', 'species'])
        ->where('organisation_id', $organisation->id)
            ->orderBy('start_date', 'desc')
            ->get();

        return $this->ok('Quota Allocations retrieved successfully', $quotaAllocations);
    }


    /**
     * Store a new quota allocation (API).
     */
    public function storeQuotaAllocation(Request $request, Organisation $organisation)
    {
        $validated = $request->validate([
            'species_id' => [
                'required',
                'exists:species,id',
                \Illuminate\Validation\Rule::unique('quota_allocations')->where(function ($query) use ($request, $organisation) {
                    return $query->where('organisation_id', $organisation->id)
                                ->where('species_id', $request->species_id)
                                ->where('period', date('Y', strtotime($request->start_date)))
                                ->whereNull('deleted_at');
                }),
            ],
            'hunting_quota' => 'required|integer|min:0',
            'rational_killing_quota' => 'required|integer|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
        ], [
            'species_id.unique' => 'A quota allocation already exists for this species for the selected year.'
        ]);

        $validated['organisation_id'] = $organisation->id;
        $validated['period'] = date('Y', strtotime($request->start_date));

        $quotaAllocation = QuotaAllocation::create($validated);
        QuotaAllocationBalance::create([
            'quota_allocation_id' => $quotaAllocation->id,
            'remaining_quota' => $validated['hunting_quota']
        ]);

        return $this->created('Quota allocation created successfully.', $quotaAllocation);
    }

    /**
     * Show a specific quota allocation (API).
     */
    public function showQuotaAllocation(Organisation $organisation, QuotaAllocation $quotaAllocation)
    {
        if ($quotaAllocation->organisation_id !== $organisation->id) {
            return $this->error('Not found', 404);
        }
        return $this->ok('Quota allocation retrieved successfully.', $quotaAllocation);
    }

    /**
     * Update a quota allocation (API).
     */
    public function updateQuotaAllocation(Request $request, Organisation $organisation, QuotaAllocation $quotaAllocation)
    {
        $validated = $request->validate([
            'species_id' => [
                'required',
                'exists:species,id',
                \Illuminate\Validation\Rule::unique('quota_allocations')->where(function ($query) use ($request, $organisation) {
                    return $query->where('organisation_id', $organisation->id)
                                ->where('species_id', $request->species_id)
                                ->where('period', date('Y', strtotime($request->start_date)))
                                ->whereNull('deleted_at');
                })->ignore($quotaAllocation->id),
            ],
            'hunting_quota' => 'required|integer|min:0',
            'rational_killing_quota' => 'required|integer|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
        ], [
            'species_id.unique' => 'A quota allocation already exists for this species for the selected year.'
        ]);

        $validated['period'] = date('Y', strtotime($request->start_date));
        $quotaAllocation->update($validated);

        return $this->ok('Quota allocation updated successfully.', $quotaAllocation);
    }

    /**
     * Delete a quota allocation (API).
     */
    public function destroyQuotaAllocation(Organisation $organisation, QuotaAllocation $quotaAllocation)
    {
        if ($quotaAllocation->organisation_id !== $organisation->id) {
            return $this->error('Not found', 404);
        }
        $quotaAllocation->delete();
        return $this->ok('Quota allocation deleted successfully.');
    }

    /**
     * Get quota allocation and balance for a species and period (API).
     */
    public function getQuotaAllocationApi(Request $request, Organisation $organisation)
    {
        $request->validate([
            'species_id' => 'required|exists:species,id',
            'period' => 'required|integer',
        ]);
        $speciesId = $request->species_id;
        $period = $request->period;
        $quotaAllocation = QuotaAllocation::where('organisation_id', $organisation->id)
            ->where('species_id', $speciesId)
            ->where('period', $period)
            ->first();
        if (!$quotaAllocation) {
            return $this->ok('No quota allocation found', [
                'quota_allocation' => null,
                'quota_balance' => null,
                'debug' => [
                    'organisation_id' => $organisation->id,
                    'species_id' => $speciesId,
                    'period' => $period,
                ]
            ]);
        }
        $quotaBalance = QuotaAllocationBalance::where('quota_allocation_id', $quotaAllocation->id)->first();
        if (!$quotaBalance) {
            $quotaBalanceData = [
                'allocated_quota' => $quotaAllocation->hunting_quota,
                'total_off_take' => 0,
                'remaining_quota' => $quotaAllocation->hunting_quota
            ];
        } else {
            $quotaBalanceData = [
                'allocated_quota' => $quotaAllocation->hunting_quota,
                'total_off_take' => $quotaAllocation->hunting_quota - $quotaBalance->remaining_quota,
                'remaining_quota' => $quotaBalance->remaining_quota
            ];
        }
        return $this->ok('Quota allocation retrieved successfully', [
            'quota_allocation' => $quotaAllocation,
            'quota_balance' => $quotaBalanceData,
            'debug' => [
                'organisation_id' => $organisation->id,
                'species_id' => $speciesId,
                'period' => $period,
            ]
        ]);
    }
}
