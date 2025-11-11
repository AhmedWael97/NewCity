<?php

namespace Database\Factories;

use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;

class CityFactory extends Factory
{
    protected $model = City::class;

    public function definition()
    {
        return [
            'name' => $this->faker->city(),
            'slug' => $this->faker->unique()->slug(),
            'state' => $this->faker->state(),
            'country' => $this->faker->country(),
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
            'description' => $this->faker->optional()->paragraph(),
            'image' => $this->faker->optional()->imageUrl(640, 480, 'city'),
            'is_active' => $this->faker->boolean(90), // 90% chance of being active
            'created_at' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'updated_at' => function (array $attributes) {
                return $this->faker->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];
    }

    /**
     * Indicate that the city is active.
     */
    public function active()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => true,
            ];
        });
    }

    /**
     * Indicate that the city is inactive.
     */
    public function inactive()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => false,
            ];
        });
    }

    /**
     * Create a city with specific country.
     */
    public function country(string $country)
    {
        return $this->state(function (array $attributes) use ($country) {
            return [
                'country' => $country,
            ];
        });
    }

    /**
     * Create Egyptian cities.
     */
    public function egyptian()
    {
        return $this->state(function (array $attributes) {
            $egyptianCities = [
                'Cairo', 'Alexandria', 'Giza', 'Shubra El-Kheima', 'Port Said',
                'Suez', 'Luxor', 'Mansoura', 'El-Mahalla El-Kubra', 'Tanta',
                'Asyut', 'Ismailia', 'Fayyum', 'Zagazig', 'Aswan'
            ];
            
            return [
                'name' => $this->faker->randomElement($egyptianCities),
                'country' => 'Egypt',
                'state' => null,
            ];
        });
    }
}