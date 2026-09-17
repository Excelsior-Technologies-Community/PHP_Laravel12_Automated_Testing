<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    /*
    |--------------------------------------------------------------------------
    | LIST PRODUCTS
    |--------------------------------------------------------------------------
    */

    #[Test]
    public function products_can_be_listed(): void
    {
        Product::factory()->count(3)->create();

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE PRODUCT
    |--------------------------------------------------------------------------
    */

    #[Test]
    public function product_can_be_created(): void
    {
        $response = $this->postJson('/api/products', [
            'name' => 'iPhone 15',
            'price' => 1200,
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'name' => 'iPhone 15',
                'price' => 1200,
            ]);

        $this->assertDatabaseHas('products', [
            'name' => 'iPhone 15',
            'price' => 1200,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | REQUIRED VALIDATION
    |--------------------------------------------------------------------------
    */

    #[Test]
    public function product_name_is_required(): void
    {
        $response = $this->postJson('/api/products', [
            'price' => 500,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('name');
    }

    #[Test]
    public function product_price_is_required(): void
    {
        $response = $this->postJson('/api/products', [
            'name' => 'Test Product',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('price');
    }

    /*
    |--------------------------------------------------------------------------
    | ADVANCED VALIDATION
    |--------------------------------------------------------------------------
    */

    #[Test]
    public function product_name_must_be_at_least_three_characters(): void
    {
        $response = $this->postJson('/api/products', [
            'name' => 'TV',
            'price' => 500,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('name');
    }

    #[Test]
    public function product_price_must_be_a_positive_integer(): void
    {
        $response = $this->postJson('/api/products', [
            'name' => 'Test Product',
            'price' => 0,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('price');
    }

    #[Test]
    public function product_price_cannot_be_negative(): void
    {
        $response = $this->postJson('/api/products', [
            'name' => 'Test Product',
            'price' => -100,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('price');
    }

    #[Test]
    public function product_name_must_be_unique(): void
    {
        Product::factory()->create([
            'name' => 'Existing Product',
            'price' => 1000,
        ]);

        $response = $this->postJson('/api/products', [
            'name' => 'Existing Product',
            'price' => 1500,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('name');
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE PRODUCT
    |--------------------------------------------------------------------------
    */

    #[Test]
    public function product_can_be_updated(): void
    {
        $product = Product::factory()->create([
            'name' => 'Old Product',
            'price' => 500,
        ]);

        $response = $this->putJson('/api/products/' . $product->id, [
            'name' => 'Updated Product',
            'price' => 1500,
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'Updated Product',
                'price' => 1500,
            ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Product',
            'price' => 1500,
        ]);
    }

    #[Test]
    public function product_update_requires_valid_name(): void
    {
        $product = Product::factory()->create();

        $response = $this->putJson('/api/products/' . $product->id, [
            'name' => 'AB',
            'price' => 1000,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('name');
    }

    #[Test]
    public function product_update_requires_valid_price(): void
    {
        $product = Product::factory()->create();

        $response = $this->putJson('/api/products/' . $product->id, [
            'name' => 'Updated Product',
            'price' => 0,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('price');
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE PRODUCT
    |--------------------------------------------------------------------------
    */

    #[Test]
    public function product_can_be_deleted(): void
    {
        $product = Product::factory()->create();

        $response = $this->deleteJson('/api/products/' . $product->id);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Product deleted successfully',
            ]);

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | NOT FOUND
    |--------------------------------------------------------------------------
    */

    #[Test]
    public function updating_non_existing_product_returns_not_found(): void
    {
        $response = $this->putJson('/api/products/999999', [
            'name' => 'Updated Product',
            'price' => 1000,
        ]);

        $response->assertStatus(404);
    }

    #[Test]
    public function deleting_non_existing_product_returns_not_found(): void
    {
        $response = $this->deleteJson('/api/products/999999');

        $response->assertStatus(404);
    }
}