<?php

namespace Database\Factories;

use App\Models\Country;
use App\Models\OffenceType;
use App\Models\PoacherType;
use App\Models\PoachingReason;
use App\Models\Poacher;
use App\Models\PoachingIncident;
use Illuminate\Database\Eloquent\Factories\Factory;

class PoacherFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Poacher::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $statuses = ['suspected', 'arrested', 'bailed', 'sentenced', 'released'];
        
        return [
            'poaching_incident_id' => PoachingIncident::inRandomOrder()->first()->id ?? null,
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'middle_name' => $this->faker->optional(0.3)->firstName(),
            'age' => $this->faker->numberBetween(18, 60),
            'status' => $this->faker->randomElement($statuses),
            'country_id' => Country::inRandomOrder()->first()->id ?? null,
            'province_id' => null,
            'city_id' => null,
            'offence_type_id' => OffenceType::inRandomOrder()->first()->id ?? null,
            'poacher_type_id' => PoacherType::inRandomOrder()->first()->id ?? null,
            'poaching_reason_id' => PoachingReason::inRandomOrder()->first()->id ?? null,
        ];
    }
} 