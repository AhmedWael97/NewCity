<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition()
    {
        $categories = [
            'Restaurant', 'Shopping', 'Entertainment', 'Health & Medical',
            'Automotive', 'Beauty & Spa', 'Education', 'Professional Services',
            'Home Services', 'Sports & Recreation', 'Travel & Tourism', 'Technology'
        ];

        return [
            'name' => $this->faker->randomElement($categories),
            'slug' => $this->faker->unique()->slug(),
            'description' => $this->faker->optional()->paragraph(),
            'icon' => $this->faker->randomElement([
                'fas fa-utensils', 'fas fa-shopping-bag', 'fas fa-film',
                'fas fa-user-md', 'fas fa-car', 'fas fa-cut',
                'fas fa-graduation-cap', 'fas fa-briefcase', 'fas fa-home',
                'fas fa-football-ball', 'fas fa-plane', 'fas fa-laptop'
            ]),
            'color' => $this->faker->hexColor(),
            'is_active' => $this->faker->boolean(90), // 90% chance of being active
            'sort_order' => $this->faker->numberBetween(1, 100),
            'parent_id' => null, // Default to top-level category
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => function (array $attributes) {
                return $this->faker->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];
    }

    /**
     * Indicate that the category is active.
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
     * Indicate that the category is inactive.
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
     * Create a subcategory.
     */
    public function subcategory(?Category $parent = null)
    {
        return $this->state(function (array $attributes) use ($parent) {
            return [
                'parent_id' => $parent ? $parent->id : Category::factory(),
            ];
        });
    }

    /**
     * Create a top-level category.
     */
    public function topLevel()
    {
        return $this->state(function (array $attributes) {
            return [
                'parent_id' => null,
            ];
        });
    }
}