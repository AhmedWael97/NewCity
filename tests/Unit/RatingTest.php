<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Rating;
use App\Models\User;
use App\Models\Shop;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RatingTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $shop;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test user and shop
        $this->user = User::factory()->create();
        $this->shop = Shop::factory()->create([
            'name' => 'Test Shop',
            'description' => 'A test shop for rating tests'
        ]);
    }

    /** @test */
    public function rating_belongs_to_user()
    {
        $rating = Rating::factory()->create([
            'user_id' => $this->user->id,
            'shop_id' => $this->shop->id
        ]);

        $this->assertInstanceOf(User::class, $rating->user);
        $this->assertEquals($this->user->id, $rating->user->id);
    }

    /** @test */
    public function rating_belongs_to_shop()
    {
        $rating = Rating::factory()->create([
            'user_id' => $this->user->id,
            'shop_id' => $this->shop->id
        ]);

        $this->assertInstanceOf(Shop::class, $rating->shop);
        $this->assertEquals($this->shop->id, $rating->shop->id);
    }

    /** @test */
    public function rating_can_have_helpful_votes_as_array()
    {
        $voter1 = User::factory()->create();
        $voter2 = User::factory()->create();
        
        $rating = Rating::factory()->create([
            'user_id' => $this->user->id,
            'shop_id' => $this->shop->id,
            'helpful_votes' => [$voter1->id, $voter2->id]
        ]);
        
        $this->assertEquals(2, count($rating->helpful_votes));
        $this->assertTrue(in_array($voter1->id, $rating->helpful_votes));
        $this->assertTrue(in_array($voter2->id, $rating->helpful_votes));
    }

    /** @test */
    public function get_stars_attribute_returns_correct_string()
    {
        $rating = Rating::factory()->create([
            'user_id' => $this->user->id,
            'shop_id' => $this->shop->id,
            'rating' => 4
        ]);

        $expectedStars = '★★★★☆';
        $this->assertEquals($expectedStars, $rating->stars);
    }

    /** @test */
    public function is_helpful_to_returns_true_when_user_voted()
    {
        $voter = User::factory()->create();
        
        $rating = Rating::factory()->create([
            'user_id' => $this->user->id,
            'shop_id' => $this->shop->id,
            'helpful_votes' => [$voter->id]
        ]);

        $this->assertTrue($rating->isHelpfulTo($voter->id));
    }

    /** @test */
    public function is_helpful_to_returns_false_when_user_not_voted()
    {
        $voter = User::factory()->create();
        
        $rating = Rating::factory()->create([
            'user_id' => $this->user->id,
            'shop_id' => $this->shop->id,
            'helpful_votes' => []
        ]);

        $this->assertFalse($rating->isHelpfulTo($voter->id));
    }

    /** @test */
    public function is_helpful_to_returns_false_for_null_helpful_votes()
    {
        $voter = User::factory()->create();
        
        $rating = Rating::factory()->create([
            'user_id' => $this->user->id,
            'shop_id' => $this->shop->id,
            'helpful_votes' => null
        ]);

        $this->assertFalse($rating->isHelpfulTo($voter->id));
    }

    /** @test */
    public function get_helpful_count_attribute_returns_correct_count()
    {
        $voter1 = User::factory()->create();
        $voter2 = User::factory()->create();
        
        // Test with helpful votes
        $rating = Rating::factory()->create([
            'user_id' => $this->user->id,
            'shop_id' => $this->shop->id,
            'helpful_votes' => [$voter1->id, $voter2->id]
        ]);
        
        $this->assertEquals(2, $rating->helpful_count);
        
        // Test with null helpful votes
        $ratingNoVotes = Rating::factory()->create([
            'user_id' => User::factory()->create()->id,
            'shop_id' => $this->shop->id,
            'helpful_votes' => null
        ]);
        
        $this->assertEquals(0, $ratingNoVotes->helpful_count);
    }

    /** @test */
    public function verified_scope_returns_only_verified_ratings()
    {
        // Create verified rating
        $verifiedRating = Rating::factory()->create([
            'user_id' => $this->user->id,
            'shop_id' => $this->shop->id,
            'is_verified' => true
        ]);

        // Create unverified rating
        $unverifiedRating = Rating::factory()->create([
            'user_id' => User::factory()->create()->id,
            'shop_id' => $this->shop->id,
            'is_verified' => false
        ]);

        $verifiedRatings = Rating::verified()->get();

        $this->assertTrue($verifiedRatings->contains($verifiedRating));
        $this->assertFalse($verifiedRatings->contains($unverifiedRating));
    }

    /** @test */
    public function for_shop_scope_filters_correctly()
    {
        $otherShop = Shop::factory()->create();
        
        // Create rating for our test shop
        $ourShopRating = Rating::factory()->create([
            'user_id' => $this->user->id,
            'shop_id' => $this->shop->id,
            'rating' => 5
        ]);

        // Create rating for other shop
        $otherShopRating = Rating::factory()->create([
            'user_id' => User::factory()->create()->id,
            'shop_id' => $otherShop->id,
            'rating' => 4
        ]);

        $ourShopRatings = Rating::forShop($this->shop->id)->get();
        $otherShopRatings = Rating::forShop($otherShop->id)->get();

        $this->assertTrue($ourShopRatings->contains($ourShopRating));
        $this->assertFalse($ourShopRatings->contains($otherShopRating));
        
        $this->assertTrue($otherShopRatings->contains($otherShopRating));
        $this->assertFalse($otherShopRatings->contains($ourShopRating));
    }

    /** @test */
    public function by_user_scope_filters_correctly()
    {
        $otherUser = User::factory()->create();
        
        // Create rating by our test user
        $ourUserRating = Rating::factory()->create([
            'user_id' => $this->user->id,
            'shop_id' => $this->shop->id,
            'rating' => 5
        ]);

        // Create rating by other user
        $otherUserRating = Rating::factory()->create([
            'user_id' => $otherUser->id,
            'shop_id' => $this->shop->id,
            'rating' => 4
        ]);

        $ourUserRatings = Rating::byUser($this->user->id)->get();
        $otherUserRatings = Rating::byUser($otherUser->id)->get();

        $this->assertTrue($ourUserRatings->contains($ourUserRating));
        $this->assertFalse($ourUserRatings->contains($otherUserRating));
        
        $this->assertTrue($otherUserRatings->contains($otherUserRating));
        $this->assertFalse($otherUserRatings->contains($ourUserRating));
    }

    /** @test */
    public function with_comments_scope_filters_correctly()
    {
        // Create rating with comment
        $ratingWithComment = Rating::factory()->create([
            'user_id' => $this->user->id,
            'shop_id' => $this->shop->id,
            'comment' => 'Great service and friendly staff!'
        ]);

        // Create rating without comment
        $ratingWithoutComment = Rating::factory()->create([
            'user_id' => User::factory()->create()->id,
            'shop_id' => $this->shop->id,
            'comment' => null
        ]);

        // Create rating with empty comment
        $ratingEmptyComment = Rating::factory()->create([
            'user_id' => User::factory()->create()->id,
            'shop_id' => $this->shop->id,
            'comment' => ''
        ]);

        $ratingsWithComments = Rating::withComments()->get();

        $this->assertTrue($ratingsWithComments->contains($ratingWithComment));
        $this->assertFalse($ratingsWithComments->contains($ratingWithoutComment));
        $this->assertFalse($ratingsWithComments->contains($ratingEmptyComment));
    }

    /** @test */
    public function rating_validation_works_correctly()
    {
        // Test valid rating values
        foreach ([1, 2, 3, 4, 5] as $validRating) {
            $rating = Rating::factory()->create([
                'user_id' => User::factory()->create()->id,
                'shop_id' => $this->shop->id,
                'rating' => $validRating
            ]);
            
            $this->assertEquals($validRating, $rating->rating);
        }
    }

    /** @test */
    public function rating_can_have_comment()
    {
        $comment = 'This is a great shop with excellent service!';
        
        $rating = Rating::factory()->create([
            'user_id' => $this->user->id,
            'shop_id' => $this->shop->id,
            'comment' => $comment
        ]);

        $this->assertEquals($comment, $rating->comment);
    }

    /** @test */
    public function rating_can_be_without_comment()
    {
        $rating = Rating::factory()->create([
            'user_id' => $this->user->id,
            'shop_id' => $this->shop->id,
            'comment' => null
        ]);

        $this->assertNull($rating->comment);
    }

    /** @test */
    public function rating_timestamps_are_set_correctly()
    {
        $rating = Rating::factory()->create([
            'user_id' => $this->user->id,
            'shop_id' => $this->shop->id
        ]);

        $this->assertNotNull($rating->created_at);
        $this->assertNotNull($rating->updated_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $rating->created_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $rating->updated_at);
    }

    /** @test */
    public function rating_casts_work_correctly()
    {
        $rating = Rating::factory()->create([
            'user_id' => $this->user->id,
            'shop_id' => $this->shop->id,
            'is_verified' => true,
            'helpful_votes' => [1, 2, 3],
            'rating' => '5'
        ]);

        // Test boolean cast
        $this->assertTrue($rating->is_verified);
        $this->assertIsBool($rating->is_verified);

        // Test array cast
        $this->assertIsArray($rating->helpful_votes);
        $this->assertEquals([1, 2, 3], $rating->helpful_votes);

        // Test integer cast
        $this->assertIsInt($rating->rating);
        $this->assertEquals(5, $rating->rating);
    }

    /** @test */
    public function rating_fillable_attributes_work()
    {
        $data = [
            'user_id' => $this->user->id,
            'shop_id' => $this->shop->id,
            'rating' => 4,
            'comment' => 'Great shop!',
            'is_verified' => true,
            'helpful_votes' => [1, 2]
        ];

        $rating = Rating::create($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $rating->{$key});
        }
    }
}