<?php

namespace Database\Factories;

use App\Models\Shop;
use App\Models\User;
use App\Models\City;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShopFactory extends Factory
{
    protected $model = Shop::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'city_id' => City::factory(),
            'category_id' => Category::factory(),
            'name' => $this->faker->company(),
            'slug' => $this->faker->unique()->slug(),
            'description' => $this->faker->paragraph(),
            'address' => $this->faker->address(),
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->optional()->safeEmail(),
            'website' => $this->faker->optional()->url(),
            'images' => $this->faker->optional()->randomElements([
                'shop1.jpg', 'shop2.jpg', 'shop3.jpg'
            ], 2),
            'opening_hours' => json_encode([
                'monday' => ['open' => '09:00', 'close' => '18:00'],
                'tuesday' => ['open' => '09:00', 'close' => '18:00'],
                'wednesday' => ['open' => '09:00', 'close' => '18:00'],
                'thursday' => ['open' => '09:00', 'close' => '18:00'],
                'friday' => ['open' => '09:00', 'close' => '18:00'],
                'saturday' => ['open' => '10:00', 'close' => '16:00'],
                'sunday' => ['open' => '10:00', 'close' => '16:00'],
            ]),
            'rating' => 0,
            'total_reviews' => 0,
            'is_verified' => $this->faker->boolean(70), // 70% chance of being verified
            'is_featured' => $this->faker->boolean(20), // 20% chance of being featured
            'status' => $this->faker->randomElement(['active', 'pending', 'inactive']),
            'created_at' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'updated_at' => function (array $attributes) {
                return $this->faker->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];
    }

    /**
     * Indicate that the shop is verified.
     */
    public function verified()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_verified' => true,
            ];
        });
    }

    /**
     * Indicate that the shop is not verified.
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_verified' => false,
            ];
        });
    }

    /**
     * Indicate that the shop is featured.
     */
    public function featured()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_featured' => true,
            ];
        });
    }

    /**
     * Indicate that the shop is not featured.
     */
    public function regular()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_featured' => false,
            ];
        });
    }

    /**
     * Set specific status.
     */
    public function status(string $status)
    {
        return $this->state(function (array $attributes) use ($status) {
            return [
                'status' => $status,
            ];
        });
    }

    /**
     * Set shop as active.
     */
    public function active()
    {
        return $this->status('active');
    }

    /**
     * Set shop as pending.
     */
    public function pending()
    {
        return $this->status('pending');
    }

    /**
     * Set shop as inactive.
     */
    public function inactive()
    {
        return $this->status('inactive');
    }

    /**
     * Set specific category.
     */
    public function category(int $categoryId)
    {
        return $this->state(function (array $attributes) use ($categoryId) {
            return [
                'category_id' => $categoryId,
            ];
        });
    }

    /**
     * Create a shop with high ratings.
     */
    public function highlyRated()
    {
        return $this->state(function (array $attributes) {
            return [
                'rating' => $this->faker->randomFloat(2, 4.0, 5.0),
                'total_reviews' => $this->faker->numberBetween(50, 500),
            ];
        });
    }

    /**
     * Create a shop with low ratings.
     */
    public function lowRated()
    {
        return $this->state(function (array $attributes) {
            return [
                'rating' => $this->faker->randomFloat(2, 1.0, 2.5),
                'total_reviews' => $this->faker->numberBetween(5, 50),
            ];
        });
    }

    /**
     * Create a shop without ratings.
     */
    public function withoutRatings()
    {
        return $this->state(function (array $attributes) {
            return [
                'rating' => 0,
                'total_reviews' => 0,
            ];
        });
    }
}