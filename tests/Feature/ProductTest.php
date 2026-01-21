<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        Role::create(['name' => 'Admin']);
        Role::create(['name' => 'Standard User']);

        // Create users
        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');

        $this->user = User::factory()->create();
        $this->user->assignRole('Standard User');
    }

    /** @test */
    public function authenticated_user_can_view_products_index()
    {
        $this->actingAs($this->user)
            ->get(route('products.index'))
            ->assertStatus(200)
            ->assertViewIs('products.index');
    }

    /** @test */
    public function guest_cannot_view_products_index()
    {
        $this->get(route('products.index'))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function user_can_create_product_with_valid_data()
    {
        $productData = [
            'title' => 'Test Product',
            'description' => 'This is a test product description with enough characters.',
            'price' => 99.99,
            'date_available' => now()->addDays(1)->format('Y-m-d'),
        ];

        $this->actingAs($this->user)
            ->post(route('products.store'), $productData)
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('products', [
            'title' => 'Test Product',
            'price' => 99.99,
            'user_id' => $this->user->id,
        ]);
    }

    /** @test */
    public function product_creation_fails_with_invalid_data()
    {
        $invalidData = [
            'title' => 'AB', // Too short
            'description' => 'Short', // Too short
            'price' => -10, // Negative
            'date_available' => now()->subDay()->format('Y-m-d'), // Past date
        ];

        $this->actingAs($this->user)
            ->post(route('products.store'), $invalidData)
            ->assertSessionHasErrors(['title', 'description', 'price', 'date_available']);

        $this->assertDatabaseCount('products', 0);
    }

    /** @test */
    public function xss_attack_is_prevented_in_product_description()
    {
        $xssData = [
            'title' => 'XSS Test Product',
            'description' => '<script>alert("XSS")</script><p>Safe content</p>',
            'price' => 49.99,
            'date_available' => now()->addDay()->format('Y-m-d'),
        ];

        $this->actingAs($this->user)
            ->post(route('products.store'), $xssData);

        $product = Product::first();
        
        // Script tags should be stripped
        $this->assertStringNotContainsString('<script>', $product->description);
        $this->assertStringContainsString('<p>Safe content</p>', $product->description);
    }

    /** @test */
    public function sql_injection_is_prevented_in_search()
    {
        Product::factory()->create(['title' => 'Legitimate Product']);

        $sqlInjection = "'; DROP TABLE products; --";

        $this->actingAs($this->user)
            ->get(route('products.index', ['keyword' => $sqlInjection]))
            ->assertStatus(200);

        // Table should still exist
        $this->assertDatabaseCount('products', 1);
    }

    /** @test */
    public function user_can_only_edit_own_products()
    {
        $ownProduct = Product::factory()->create(['user_id' => $this->user->id]);
        $otherProduct = Product::factory()->create(['user_id' => $this->admin->id]);

        // Can edit own product
        $this->actingAs($this->user)
            ->get(route('products.edit', $ownProduct))
            ->assertStatus(200);

        // Cannot edit other's product
        $this->actingAs($this->user)
            ->get(route('products.edit', $otherProduct))
            ->assertStatus(403);
    }

    /** @test */
    public function admin_can_edit_any_product()
    {
        $userProduct = Product::factory()->create(['user_id' => $this->user->id]);

        $this->actingAs($this->admin)
            ->get(route('products.edit', $userProduct))
            ->assertStatus(200);
    }

    /** @test */
    public function search_returns_correct_results()
    {
        Product::factory()->create(['title' => 'Laravel Book']);
        Product::factory()->create(['title' => 'PHP Guide']);
        Product::factory()->create(['title' => 'JavaScript Tutorial']);

        $this->actingAs($this->user)
            ->get(route('products.index', ['keyword' => 'Laravel']))
            ->assertStatus(200)
            ->assertSee('Laravel Book')
            ->assertDontSee('JavaScript Tutorial');
    }

    /** @test */
    public function price_filter_works_correctly()
    {
        Product::factory()->create(['title' => 'Cheap Item', 'price' => 10.00]);
        Product::factory()->create(['title' => 'Medium Item', 'price' => 50.00]);
        Product::factory()->create(['title' => 'Expensive Item', 'price' => 100.00]);

        $this->actingAs($this->user)
            ->get(route('products.index', ['min_price' => 20, 'max_price' => 80]))
            ->assertStatus(200)
            ->assertSee('Medium Item')
            ->assertDontSee('Cheap Item')
            ->assertDontSee('Expensive Item');
    }

    /** @test */
    public function csrf_token_is_required_for_mutations()
    {
        $productData = [
            'title' => 'Test Product',
            'description' => 'Description here',
            'price' => 49.99,
            'date_available' => now()->addDay()->format('Y-m-d'),
        ];

        // Without CSRF token
        $this->actingAs($this->user)
            ->post(route('products.store'), $productData, [
                'X-CSRF-TOKEN' => 'invalid-token'
            ])
            ->assertStatus(419); // CSRF token mismatch
    }

    /** @test */
    public function soft_delete_works_correctly()
    {
        $product = Product::factory()->create(['user_id' => $this->user->id]);

        $this->actingAs($this->user)
            ->delete(route('products.destroy', $product))
            ->assertRedirect();

        $this->assertSoftDeleted('products', ['id' => $product->id]);
        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }
}