<?php

namespace Database\Factories;

use App\Models\Street;
use Illuminate\Database\Eloquent\Factories\Factory;

class StreetFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Street::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'postal_code' => $this->faker->regexify('[0-9]{5}'),
            'name' => $this->faker->streetName(),
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
        ];
    }
}
