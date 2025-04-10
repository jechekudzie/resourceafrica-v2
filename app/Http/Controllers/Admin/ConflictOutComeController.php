<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\ConflictOutcome;
use App\Models\ConflictType;
use Illuminate\Http\Request;

class ConflictOutcomeController extends Controller
{
    //index method passing the data to the view
    public function index()
    {
        $conflictOutcomes = ConflictOutcome::all();
        $conflictTypes = ConflictType::all();
        return view('admin.conflict_outcomes.index', compact('conflictOutcomes','conflictTypes'));
    }

    //store method
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:conflict_outcomes,name',
            'conflict_type_id' => 'required|exists:conflict_types,id',
        ]);

        $conflictOutcome = ConflictOutcome::create([
            'name' => $validated['name'],
            'conflict_type_id' => $validated['conflict_type_id'],
        ]);

        return redirect()->route('admin.conflict-outcomes.index')->with('success', 'Conflict Outcome created successfully');
    }

    //update method
    public function update(Request $request, ConflictOutcome $conflictOutcome)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'conflict_type_id' => 'required|exists:conflict_types,id',
        ]);

        $conflictOutcome->update([
            'name' => $validated['name'],
            'conflict_type_id' => $validated['conflict_type_id'],
        ]);

        return redirect()->route('admin.conflict-outcomes.index')->with('success', 'Conflict Outcome updated successfully');
    }
}
