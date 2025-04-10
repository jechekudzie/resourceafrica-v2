<?php

namespace App\Http\Controllers\Organisation;

use App\Http\Controllers\Controller;
use App\Models\HuntingActivity;
use App\Models\Organisation;
use App\Models\Species;
use App\Models\QuotaAllocation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HuntingDashboardController extends Controller
{
    public function index(Request $request, Organisation $organisation)
    {
        // Get the current year if not specified
        $year = $request->input('year', Carbon::now()->year);
        
        // Get all primary organizations
        $primaryOrganisations = Organisation::where('is_primary', 1)->get();
        
        // Get hunting activities data
        $huntingActivities = HuntingActivity::with(['species', 'huntingConcession'])
            ->where('organisation_id', $organisation->id)
            ->whereYear('start_date', $year)
            ->get();
            
        // Get quota allocations
        $quotaAllocations = QuotaAllocation::with('species')
            ->where('organisation_id', $organisation->id)
            ->where('period', $year)
            ->get();
            
        // Group hunting activities by month
        $monthlyActivities = $huntingActivities
            ->groupBy(function($activity) {
                return Carbon::parse($activity->start_date)->format('F');
            });
            
        // Calculate species utilization
        $speciesUtilization = [];
        $species = Species::all();
        
        foreach ($species as $specie) {
            $allocated = $quotaAllocations
                ->where('species_id', $specie->id)
                ->sum('hunting_quota');
                
            $utilized = $huntingActivities
                ->flatMap->species
                ->where('id', $specie->id)
                ->sum('pivot.off_take');
                
            if ($allocated > 0) {
                $speciesUtilization[] = [
                    'species' => $specie->name,
                    'allocated' => $allocated,
                    'utilized' => $utilized,
                    'percentage' => round(($utilized / $allocated) * 100, 2)
                ];
            }
        }
        
        // Group activities by district/concession
        $districtActivities = $huntingActivities
            ->groupBy('huntingConcession.name')
            ->map(function($activities) {
                return [
                    'total_activities' => $activities->count(),
                    'species_count' => $activities->flatMap->species->unique()->count(),
                    'total_offtake' => $activities->flatMap->species->sum('pivot.off_take')
                ];
            });
            
        return view('organisation.hunting-dashboard.index', compact(
            'organisation',
            'primaryOrganisations',
            'year',
            'monthlyActivities',
            'speciesUtilization',
            'districtActivities'
        ));
    }
} 