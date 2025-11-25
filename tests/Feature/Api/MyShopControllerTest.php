<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use App\Models\Shop;
use App\Models\City;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class MyShopControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $shopOwner;
    protected $regularUser;
    protected $city;
    protected $category;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a city
        $this->city = City::factory()->create([
            'name' => 'Test City',
            'slug' => 'test-city',
            'is_active' => true
        ]);

        // Create a category
        $this->category = Category::factory()->create([
            'name' => 'Test Category',
            'slug' => 'test-category',
            'is_active' => true
        ]);

        // Create a shop owner user
        $this->shopOwner = User::factory()->create([
            'user_type' => User::TYPE_SHOP_OWNER,
            'email' => 'shopowner@test.com',
            'name' => 'Shop Owner'
        ]);

        // Create a regular user
        $this->regularUser = User::factory()->create([
            'user_type' => User::TYPE_REGULAR,
            'email' => 'regular@test.com',
            'name' => 'Regular User'
        ]);
    }

    /** @test */
    public function shop_owner_can_create_shop()
    {
        Sanctum::actingAs($this->shopOwner);

        $shopData = [
            'name' => 'My Test Shop',
            'description' => 'This is a test shop',
            'city_id' => $this->city->id,
            'category_id' => $this->category->id,
            'address' => '123 Test Street',
            'latitude' => 40.7128,
            'longitude' => -74.0060,
            'phone' => '+1234567890',
            'email' => 'shop@test.com',
            'website' => 'https://testshop.com'
        ];

        $response = $this->postJson('/api/v1/my-shops', $shopData);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Shop created successfully'
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'slug',
                    'description',
                    'address',
                    'latitude',
                    'longitude',
                    'phone',
                    'email',
                    'website',
                    'user_id',
                    'city_id',
                    'category_id',
                    'created_at',
                    'updated_at'
                ]
            ]);

        $this->assertDatabaseHas('shops', [
            'name' => 'My Test Shop',
            'user_id' => $this->shopOwner->id,
            'city_id' => $this->city->id,
            'category_id' => $this->category->id
        ]);
    }

    /** @test */
    public function regular_user_cannot_create_shop()
    {
        Sanctum::actingAs($this->regularUser);

        $shopData = [
            'name' => 'My Test Shop',
            'description' => 'This is a test shop',
            'city_id' => $this->city->id,
            'category_id' => $this->category->id,
            'address' => '123 Test Street',
            'latitude' => 40.7128,
            'longitude' => -74.0060
        ];

        $response = $this->postJson('/api/v1/my-shops', $shopData);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Only shop owners can create shops'
            ]);

        $this->assertDatabaseMissing('shops', [
            'name' => 'My Test Shop',
            'user_id' => $this->regularUser->id
        ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_create_shop()
    {
        $shopData = [
            'name' => 'My Test Shop',
            'city_id' => $this->city->id,
            'category_id' => $this->category->id,
            'address' => '123 Test Street',
            'latitude' => 40.7128,
            'longitude' => -74.0060
        ];

        $response = $this->postJson('/api/v1/my-shops', $shopData);

        $response->assertStatus(401);
    }

    /** @test */
    public function shop_creation_requires_mandatory_fields()
    {
        Sanctum::actingAs($this->shopOwner);

        $response = $this->postJson('/api/v1/my-shops', []);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validation failed'
            ])
            ->assertJsonValidationErrors([
                'name',
                'city_id',
                'category_id',
                'address',
                'latitude',
                'longitude'
            ]);
    }

    /** @test */
    public function shop_owner_can_view_their_shops()
    {
        Sanctum::actingAs($this->shopOwner);

        // Create shops for the shop owner
        Shop::factory()->count(3)->create([
            'user_id' => $this->shopOwner->id,
            'city_id' => $this->city->id,
            'category_id' => $this->category->id
        ]);

        $response = $this->getJson('/api/v1/my-shops');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true
            ])
            ->assertJsonStructure([
                'success',
                'data' => [
                    'data',
                    'current_page',
                    'last_page',
                    'per_page',
                    'total'
                ]
            ]);

        $this->assertEquals(3, $response->json('data.total'));
    }

    /** @test */
    public function shop_owner_can_view_single_shop()
    {
        Sanctum::actingAs($this->shopOwner);

        $shop = Shop::factory()->create([
            'user_id' => $this->shopOwner->id,
            'city_id' => $this->city->id,
            'category_id' => $this->category->id
        ]);

        $response = $this->getJson("/api/v1/my-shops/{$shop->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true
            ])
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'name',
                    'slug',
                    'user_id'
                ]
            ]);

        $this->assertEquals($shop->id, $response->json('data.id'));
    }

    /** @test */
    public function shop_owner_cannot_view_other_users_shop()
    {
        Sanctum::actingAs($this->shopOwner);

        $otherUser = User::factory()->create(['user_type' => User::TYPE_SHOP_OWNER]);
        $shop = Shop::factory()->create([
            'user_id' => $otherUser->id,
            'city_id' => $this->city->id,
            'category_id' => $this->category->id
        ]);

        $response = $this->getJson("/api/v1/my-shops/{$shop->id}");

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthorized'
            ]);
    }

    /** @test */
    public function shop_owner_can_update_their_shop()
    {
        Sanctum::actingAs($this->shopOwner);

        $shop = Shop::factory()->create([
            'user_id' => $this->shopOwner->id,
            'city_id' => $this->city->id,
            'category_id' => $this->category->id
        ]);

        $updatedData = [
            'name' => 'Updated Shop Name',
            'description' => 'Updated description',
            'category_id' => $this->category->id,
            'address' => 'Updated Address',
            'latitude' => 41.0000,
            'longitude' => -75.0000,
            'phone' => '+9876543210'
        ];

        $response = $this->putJson("/api/v1/my-shops/{$shop->id}", $updatedData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Shop updated successfully'
            ]);

        $this->assertDatabaseHas('shops', [
            'id' => $shop->id,
            'name' => 'Updated Shop Name',
            'address' => 'Updated Address',
            'phone' => '+9876543210'
        ]);
    }

    /** @test */
    public function shop_owner_cannot_update_other_users_shop()
    {
        Sanctum::actingAs($this->shopOwner);

        $otherUser = User::factory()->create(['user_type' => User::TYPE_SHOP_OWNER]);
        $shop = Shop::factory()->create([
            'user_id' => $otherUser->id,
            'city_id' => $this->city->id,
            'category_id' => $this->category->id
        ]);

        $response = $this->putJson("/api/v1/my-shops/{$shop->id}", [
            'name' => 'Hacked Shop Name',
            'category_id' => $this->category->id,
            'address' => 'Hacked Address',
            'latitude' => 41.0000,
            'longitude' => -75.0000
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthorized'
            ]);
    }

    /** @test */
    public function shop_owner_can_delete_their_shop()
    {
        Sanctum::actingAs($this->shopOwner);

        $shop = Shop::factory()->create([
            'user_id' => $this->shopOwner->id,
            'city_id' => $this->city->id,
            'category_id' => $this->category->id
        ]);

        $response = $this->deleteJson("/api/v1/my-shops/{$shop->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Shop deleted successfully'
            ]);

        $this->assertSoftDeleted('shops', [
            'id' => $shop->id
        ]);
    }

    /** @test */
    public function shop_owner_cannot_delete_other_users_shop()
    {
        Sanctum::actingAs($this->shopOwner);

        $otherUser = User::factory()->create(['user_type' => User::TYPE_SHOP_OWNER]);
        $shop = Shop::factory()->create([
            'user_id' => $otherUser->id,
            'city_id' => $this->city->id,
            'category_id' => $this->category->id
        ]);

        $response = $this->deleteJson("/api/v1/my-shops/{$shop->id}");

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthorized'
            ]);

        $this->assertDatabaseHas('shops', [
            'id' => $shop->id
        ]);
    }

    /** @test */
    public function shop_slug_is_automatically_generated_from_name()
    {
        Sanctum::actingAs($this->shopOwner);

        $response = $this->postJson('/api/v1/my-shops', [
            'name' => 'Amazing Coffee Shop',
            'city_id' => $this->city->id,
            'category_id' => $this->category->id,
            'address' => '123 Test Street',
            'latitude' => 40.7128,
            'longitude' => -74.0060
        ]);

        $response->assertStatus(201);
        
        $shop = Shop::where('user_id', $this->shopOwner->id)
            ->where('name', 'Amazing Coffee Shop')
            ->first();

        $this->assertNotNull($shop);
        $this->assertStringStartsWith('amazing-coffee-shop-', $shop->slug);
    }

    /** @test */
    public function shop_slug_updates_when_name_changes()
    {
        Sanctum::actingAs($this->shopOwner);

        $shop = Shop::factory()->create([
            'user_id' => $this->shopOwner->id,
            'city_id' => $this->city->id,
            'category_id' => $this->category->id,
            'name' => 'Original Name'
        ]);

        $originalSlug = $shop->slug;

        $response = $this->putJson("/api/v1/my-shops/{$shop->id}", [
            'name' => 'New Amazing Name',
            'category_id' => $this->category->id,
            'address' => $shop->address,
            'latitude' => $shop->latitude,
            'longitude' => $shop->longitude
        ]);

        $response->assertStatus(200);

        $shop->refresh();
        
        $this->assertNotEquals($originalSlug, $shop->slug);
        $this->assertStringStartsWith('new-amazing-name-', $shop->slug);
    }
}
