<?php

namespace Database\Seeders;

use App\Models\ConflictOutcome;
use App\Models\ConflictType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConflictOutcomeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $conflictTypes = ConflictType::all()->keyBy('name');
        $conflictOutcomes = [
            ['name' => 'Crop destruction', 'type' => 'Human - Wildlife'],
            ['name' => 'Livestock death', 'type' => 'Wildlife - Human'],
            ['name' => 'Human injury', 'type' => 'Human - Wildlife'],
            ['name' => 'Human death', 'type' => 'Human - Wildlife'],
            ['name' => 'Property damage', 'type' => 'Human - Wildlife'],
            ['name' => 'Other', 'type' => 'Wildlife - Human'],
        ];

        foreach ($conflictOutcomes as $conflictOutcome) {
            ConflictOutcome::create([
                'name' => $conflictOutcome['name'],
                'conflict_type_id' => $conflictTypes[$conflictOutcome['type']]->id,
            ]);
        }
    }
}
