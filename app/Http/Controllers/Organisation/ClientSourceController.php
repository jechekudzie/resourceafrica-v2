<?php

namespace App\Http\Controllers\Organisation;

use App\Http\Controllers\Controller;
use App\Models\ClientSource;
use App\Models\Organisation;
use Illuminate\Http\Request;

class ClientSourceController extends Controller
{
    public function index(Organisation $organisation)
    {
        $clientSources = $organisation->clientSources()
            ->orderBy('period', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        return view('organisation.client-sources.index', compact('organisation', 'clientSources'));
    }

    public function create(Organisation $organisation)
    {
        return view('organisation.client-sources.create', compact('organisation'));
    }

    public function store(Request $request, Organisation $organisation)
    {
        $validated = $request->validate([
            'period' => 'required|integer|min:2019|max:' . date('Y'),
            'month' => 'nullable|integer|min:1|max:12',
            'north_america' => 'required|integer|min:0',
            'europe_asia' => 'required|integer|min:0',
            'africa' => 'required|integer|min:0',
            'asia' => 'required|integer|min:0',
            'middle_east' => 'required|integer|min:0',
            'south_america' => 'required|integer|min:0',
            'oceania' => 'required|integer|min:0',
        ]);

        $organisation->clientSources()->create($validated);

        return redirect()
            ->route('organisation.client-sources.index', $organisation)
            ->with('success', 'Client source record created successfully.');
    }

    public function show(Organisation $organisation, ClientSource $clientSource)
    {
        return view('organisation.client-sources.show', compact('organisation', 'clientSource'));
    }

    public function edit(Organisation $organisation, ClientSource $clientSource)
    {
        return view('organisation.client-sources.edit', compact('organisation', 'clientSource'));
    }

    public function update(Request $request, Organisation $organisation, ClientSource $clientSource)
    {
        $validated = $request->validate([
            'period' => 'required|integer|min:2019|max:' . date('Y'),
            'month' => 'nullable|integer|min:1|max:12',
            'north_america' => 'required|integer|min:0',
            'europe_asia' => 'required|integer|min:0',
            'africa' => 'required|integer|min:0',
            'asia' => 'required|integer|min:0',
            'middle_east' => 'required|integer|min:0',
            'south_america' => 'required|integer|min:0',
            'oceania' => 'required|integer|min:0',
        ]);

        $clientSource->update($validated);

        return redirect()
            ->route('organisation.client-sources.index', $organisation)
            ->with('success', 'Client source record updated successfully.');
    }

    public function destroy(Organisation $organisation, ClientSource $clientSource)
    {
        $clientSource->delete();

        return redirect()
            ->route('organisation.client-sources.index', $organisation)
            ->with('success', 'Client source record deleted successfully.');
    }
} 