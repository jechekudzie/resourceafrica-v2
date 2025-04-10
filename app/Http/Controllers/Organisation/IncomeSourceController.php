<?php

namespace App\Http\Controllers\Organisation;

use App\Http\Controllers\Controller;
use App\Models\Organisation;
use App\Models\IncomeSource;
use Illuminate\Http\Request;

class IncomeSourceController extends Controller
{
    /**
     * Display a listing of income sources for an organisation.
     */
    public function index(Organisation $organisation)
    {
        $incomeSources = $organisation->incomeSources()
            ->orderBy('period', 'desc')
            ->orderBy('month', 'desc')
            ->paginate(10);

        return view('organisation.income-sources.index', compact('organisation', 'incomeSources'));
    }

    /**
     * Show the form for creating a new income source.
     */
    public function create(Organisation $organisation)
    {
        return view('organisation.income-sources.create', compact('organisation'));
    }

    /**
     * Store a newly created income source in storage.
     */
    public function store(Request $request, Organisation $organisation)
    {
        $validated = $request->validate([
            'period' => 'required|integer|min:1900|max:2100',
            'month' => 'required|integer|min:1|max:12',
            'trophy_fee_amount' => 'required|numeric|min:0',
            'hides_amount' => 'required|numeric|min:0',
            'meat_amount' => 'required|numeric|min:0',
            'hunting_concession_fee_amount' => 'required|numeric|min:0',
            'photographic_fee_amount' => 'required|numeric|min:0',
            'other_amount' => 'required|numeric|min:0',
            'other_description' => 'nullable|string|max:1000',
        ]);

        $organisation->incomeSources()->create($validated);

        return redirect()
            ->route('organisation.income-sources.index', $organisation)
            ->with('success', 'Income source record created successfully.');
    }

    /**
     * Show the form for editing the specified income source.
     */
    public function edit(Organisation $organisation, IncomeSource $incomeSource)
    {
        return view('organisation.income-sources.edit', compact('organisation', 'incomeSource'));
    }

    /**
     * Update the specified income source in storage.
     */
    public function update(Request $request, Organisation $organisation, IncomeSource $incomeSource)
    {
        $validated = $request->validate([
            'period' => 'required|integer|min:1900|max:2100',
            'month' => 'required|integer|min:1|max:12',
            'trophy_fee_amount' => 'required|numeric|min:0',
            'hides_amount' => 'required|numeric|min:0',
            'meat_amount' => 'required|numeric|min:0',
            'hunting_concession_fee_amount' => 'required|numeric|min:0',
            'photographic_fee_amount' => 'required|numeric|min:0',
            'other_amount' => 'required|numeric|min:0',
            'other_description' => 'nullable|string|max:1000',
        ]);

        $incomeSource->update($validated);

        return redirect()
            ->route('organisation.income-sources.index', $organisation)
            ->with('success', 'Income source record updated successfully.');
    }

    /**
     * Remove the specified income source from storage.
     */
    public function destroy(Organisation $organisation, IncomeSource $incomeSource)
    {
        $incomeSource->delete();

        return redirect()
            ->route('organisation.income-sources.index', $organisation)
            ->with('success', 'Income source record deleted successfully.');
    }
} 