<?php

namespace Database\Seeders;

use App\Models\ConflictType;
use App\Models\ControlMeasure;
use App\Models\Organisation;
use App\Models\OrganisationType;
use App\Models\HuntingActivity;
use App\Models\HuntingActivitySpecies;
use App\Models\HuntingConcession;
use App\Models\Poacher;
use App\Models\PoachingIncident;
use App\Models\PoachingIncidentMethod;
use App\Models\PoachingIncidentSpecies;
use App\Models\ProblemAnimalControl;
use App\Models\QuotaAllocation;
use App\Models\Species;
use App\Models\WildlifeConflictIncident;
use Illuminate\Database\Seeder;

class DashboardDummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all Rural District Council organizations
        $organisationType = OrganisationType::where('name', 'like', '%Rural District Council%')->first();
        if (!$organisationType) {
            $this->command->info('Rural District Council organization type not found. Skipping dummy data generation.');
            return;
        }
        
        $rdcOrganisations = Organisation::where('organisation_type_id', $organisationType->id)->get();
        if ($rdcOrganisations->isEmpty()) {
            $this->command->info('No Rural District Council organizations found. Skipping dummy data generation.');
            return;
        }
        
        // Get all available species
        $species = Species::all();
        if ($species->isEmpty()) {
            $this->command->info('No species found. Skipping dummy data generation.');
            return;
        }
        
        // Make sure we have conflict types for wildlife conflicts
        $conflictTypes = ConflictType::all();
        if ($conflictTypes->isEmpty()) {
            $this->command->info('No conflict types found. Skipping wildlife conflict incidents generation.');
        }
        
        // Make sure we have control measures for problem animal control
        $controlMeasures = ControlMeasure::all();
        if ($controlMeasures->isEmpty()) {
            $this->command->info('No control measures found. This might affect problem animal control records.');
        }
        
        foreach ($rdcOrganisations as $rdc) {
            $this->command->info("Generating dummy data for {$rdc->name}...");
            
            // Create hunting concessions for each RDC
            $concessions = [];
            for ($i = 0; $i < rand(2, 5); $i++) {
                $concession = HuntingConcession::factory()->create([
                    'organisation_id' => $rdc->id,
                    'name' => "Concession " . ($i + 1) . " - " . $rdc->name,
                ]);
                $concessions[] = $concession;
            }
            
            // Create quota allocations for each species and RDC
            foreach ($species->random(rand(5, 10)) as $s) {
                QuotaAllocation::factory()->create([
                    'organisation_id' => $rdc->id,
                    'species_id' => $s->id,
                ]);
            }
            
            // Create hunting activities
            $huntingActivities = [];
            for ($i = 0; $i < rand(5, 15); $i++) {
                $huntingActivity = HuntingActivity::factory()->create([
                    'organisation_id' => $rdc->id,
                    'hunting_concession_id' => $concessions[array_rand($concessions)]->id,
                ]);
                
                // Add species to hunting activity
                foreach ($species->random(rand(1, 3)) as $s) {
                    HuntingActivitySpecies::create([
                        'hunting_activity_id' => $huntingActivity->id,
                        'species_id' => $s->id,
                        'off_take' => rand(1, 5),
                    ]);
                }
                
                $huntingActivities[] = $huntingActivity;
            }
            
            // Create wildlife conflict incidents
            $conflictIncidents = [];
            for ($i = 0; $i < rand(8, 20); $i++) {
                $conflictIncident = WildlifeConflictIncident::factory()->create([
                    'organisation_id' => $rdc->id,
                    'conflict_type_id' => $conflictTypes->isNotEmpty() ? $conflictTypes->random()->id : null,
                ]);
                
                // Add species to conflict incident
                foreach ($species->random(rand(1, 2)) as $s) {
                    $conflictIncident->species()->attach($s->id);
                }
                
                $conflictIncidents[] = $conflictIncident;
            }
            
            // Create problem animal control records
            for ($i = 0; $i < rand(5, 15); $i++) {
                $pac = ProblemAnimalControl::factory()->create([
                    'organisation_id' => $rdc->id,
                    'wildlife_conflict_incident_id' => !empty($conflictIncidents) ? $conflictIncidents[array_rand($conflictIncidents)]->id : null,
                ]);
                
                // Attach control measures if available
                if ($controlMeasures->isNotEmpty()) {
                    $pac->controlMeasures()->attach(
                        $controlMeasures->random(rand(1, 3))->pluck('id')->toArray()
                    );
                }
            }
            
            // Create poaching incidents
            for ($i = 0; $i < rand(5, 15); $i++) {
                $poachingIncident = PoachingIncident::factory()->create([
                    'organisation_id' => $rdc->id,
                ]);
                
                // Add species to poaching incident
                foreach ($species->random(rand(1, 3)) as $s) {
                    PoachingIncidentSpecies::create([
                        'poaching_incident_id' => $poachingIncident->id,
                        'species_id' => $s->id,
                        'estimate_number' => rand(1, 5),
                    ]);
                }
                
                // Create poachers
                for ($j = 0; $j < rand(1, 4); $j++) {
                    Poacher::factory()->create([
                        'poaching_incident_id' => $poachingIncident->id,
                    ]);
                }
            }
        }
        
        $this->command->info('Dummy data generated successfully!');
    }
} 