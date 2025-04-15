<?php

namespace Database\Seeders;


use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\HuntingOutcome;
class HuntingOutComeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        $outComes = [
            ['name' => 'Killed'],
            ['name' => 'Injured'],
            ['name' => 'Injured and Escaped'],
            ['name' => 'Missed'],
            // Add more outcomes as necessary
        ];

        foreach ($outComes as $key => $outcome) {
            HuntingOutCome::create($outcome);
        }
    }
}
