<?php

namespace Database\Factories;

use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'city_id' => City::factory(),
            'user_type' => fake()->randomElement(['regular', 'shop_owner', 'admin']),
            'is_verified' => fake()->boolean(70), // 70% chance of being verified
            'address' => fake()->optional()->address(),
            'date_of_birth' => fake()->optional()->dateTimeBetween('-65 years', '-18 years'),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Set specific user type.
     */
    public function userType(string $type): static
    {
        return $this->state(fn (array $attributes) => [
            'user_type' => $type,
        ]);
    }

    /**
     * Create a regular user.
     */
    public function regular(): static
    {
        return $this->userType('regular');
    }

    /**
     * Create a shop owner user.
     */
    public function shopOwner(): static
    {
        return $this->userType('shop_owner');
    }

    /**
     * Create an admin user.
     */
    public function admin(): static
    {
        return $this->userType('admin');
    }

    /**
     * Create a verified user.
     */
    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_verified' => true,
        ]);
    }

    /**
     * Create a user without verification.
     */
    public function notVerified(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_verified' => false,
        ]);
    }
}
