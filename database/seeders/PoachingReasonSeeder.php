<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PoachingReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //poaching reasons subsistence or commercial
        $poachingReasons = [
            'Subsistence',
            'Commercial',
        ];
        foreach ($poachingReasons as $reason) {
            \App\Models\PoachingReason::create([
                'name' => $reason,
            ]);
        }
    }
}
