<?php

namespace App\Http\Controllers\Organisation;

use App\Http\Controllers\Controller;
use App\Models\Organisation;
use App\Models\IncomeUsage;
use Illuminate\Http\Request;

class IncomeUsageController extends Controller
{
    /**
     * Display a listing of income usages for an organisation.
     */
    public function index(Organisation $organisation)
    {
        $incomeUsages = $organisation->incomeUsages()
            ->orderBy('period', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        return view('organisation.income-usages.index', compact('organisation', 'incomeUsages'));
    }

    /**
     * Show the form for creating a new income usage.
     */
    public function create(Organisation $organisation)
    {
        return view('organisation.income-usages.create', compact('organisation'));
    }

    /**
     * Store a newly created income usage in storage.
     */
    public function store(Request $request, Organisation $organisation)
    {
        $validated = $request->validate([
            'period' => 'required|integer|min:2019|max:' . date('Y'),
            'month' => 'required|integer|min:1|max:12',
            'administration_amount' => 'required|numeric|min:0',
            'management_activities_amount' => 'required|numeric|min:0',
            'social_services_amount' => 'required|numeric|min:0',
            'law_enforcement_amount' => 'required|numeric|min:0',
            'other_amount' => 'required|numeric|min:0',
            'other_description' => 'nullable|string|max:1000',
        ]);

        $organisation->incomeUsages()->create($validated);

        return redirect()
            ->route('organisation.income-usages.index', $organisation)
            ->with('success', 'Income usage record created successfully.');
    }

    /**
     * Show the form for editing the specified income usage.
     */
    public function edit(Organisation $organisation, IncomeUsage $incomeUsage)
    {
        return view('organisation.income-usages.edit', compact('organisation', 'incomeUsage'));
    }

    /**
     * Update the specified income usage in storage.
     */
    public function update(Request $request, Organisation $organisation, IncomeUsage $incomeUsage)
    {
        $validated = $request->validate([
            'period' => 'required|integer|min:2019|max:' . date('Y'),
            'month' => 'required|integer|min:1|max:12',
            'administration_amount' => 'required|numeric|min:0',
            'management_activities_amount' => 'required|numeric|min:0',
            'social_services_amount' => 'required|numeric|min:0',
            'law_enforcement_amount' => 'required|numeric|min:0',
            'other_amount' => 'required|numeric|min:0',
            'other_description' => 'nullable|string|max:1000',
        ]);

        $incomeUsage->update($validated);

        return redirect()
            ->route('organisation.income-usages.index', $organisation)
            ->with('success', 'Income usage record updated successfully.');
    }

    /**
     * Remove the specified income usage from storage.
     */
    public function destroy(Organisation $organisation, IncomeUsage $incomeUsage)
    {
        $incomeUsage->delete();

        return redirect()
            ->route('organisation.income-usages.index', $organisation)
            ->with('success', 'Income usage record deleted successfully.');
    }
} 