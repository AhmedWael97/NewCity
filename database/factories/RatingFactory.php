<?php

namespace Database\Factories;

use App\Models\Rating;
use App\Models\User;
use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;

class RatingFactory extends Factory
{
    protected $model = Rating::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'shop_id' => Shop::factory(),
            'rating' => $this->faker->numberBetween(1, 5),
            'comment' => $this->faker->optional(0.7)->paragraph(),
            'is_verified' => $this->faker->boolean(80), // 80% chance of being verified
            'helpful_votes' => $this->faker->optional(0.3)->randomElements(
                range(1, 10), // Random user IDs
                $this->faker->numberBetween(1, 5)
            ),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => function (array $attributes) {
                return $this->faker->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];
    }

    /**
     * Indicate that the rating is verified.
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
     * Indicate that the rating is not verified.
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
     * Create a rating with a specific star value.
     */
    public function stars(int $stars)
    {
        return $this->state(function (array $attributes) use ($stars) {
            return [
                'rating' => max(1, min(5, $stars)), // Ensure rating is between 1-5
            ];
        });
    }

    /**
     * Create a rating without a comment.
     */
    public function withoutComment()
    {
        return $this->state(function (array $attributes) {
            return [
                'comment' => null,
            ];
        });
    }

    /**
     * Create a rating with a specific comment.
     */
    public function withComment(string $comment)
    {
        return $this->state(function (array $attributes) use ($comment) {
            return [
                'comment' => $comment,
            ];
        });
    }

    /**
     * Create recent ratings (within last week).
     */
    public function recent()
    {
        return $this->state(function (array $attributes) {
            return [
                'created_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
                'updated_at' => function (array $attributes) {
                    return $this->faker->dateTimeBetween($attributes['created_at'], 'now');
                },
            ];
        });
    }

    /**
     * Create old ratings (older than 6 months).
     */
    public function old()
    {
        return $this->state(function (array $attributes) {
            return [
                'created_at' => $this->faker->dateTimeBetween('-2 years', '-6 months'),
                'updated_at' => function (array $attributes) {
                    return $this->faker->dateTimeBetween($attributes['created_at'], '-6 months');
                },
            ];
        });
    }
}