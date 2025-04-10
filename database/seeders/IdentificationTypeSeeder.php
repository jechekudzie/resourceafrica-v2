<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IdentificationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //

        $types = [
            ['name' => 'National Identification Card'],
            ['name' => 'Passport'],

        ];

        foreach ($types as $key => $type) {
            \App\Models\IdentificationType::create($type);
        }
    }
}
