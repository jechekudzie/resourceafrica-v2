<?php

namespace Database\Factories\Organisation;

use App\Models\Admin\Organisation;
use App\Models\Organisation\HuntingConcession;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Organisation\HuntingConcession>
 */
class HuntingConcessionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = HuntingConcession::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $latitude = $this->faker->latitude(-22.0, -15.0);  // Zimbabwe coordinates
        $longitude = $this->faker->longitude(25.0, 33.0);  // Zimbabwe coordinates
        
        return [
            'organisation_id' => Organisation::inRandomOrder()->first()->id,
            'name' => $this->faker->words(3, true) . ' Concession',
            'description' => $this->faker->paragraph(),
            'hectarage' => $this->faker->numberBetween(1000, 50000),
            'latitude' => $latitude,
            'longitude' => $longitude,
            'safari_id' => null,
            'slug' => null
        ];
    }
} 