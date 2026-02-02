<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function products_can_be_listed(): void
    {
        Product::factory()->count(3)->create();

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    #[Test]
    public function product_can_be_created(): void
    {
        $response = $this->postJson('/api/products', [
            'name' => 'iPhone 15',
            'price' => 1200,
        ]);

        $response->assertStatus(201)
                 ->assertJsonFragment([
                     'name' => 'iPhone 15'
                 ]);

        $this->assertDatabaseHas('products', [
            'name' => 'iPhone 15'
        ]);
    }

    #[Test]
    public function product_name_is_required(): void
    {
        $response = $this->postJson('/api/products', [
            'price' => 500
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors('name');
    }

    #[Test]
    public function product_price_is_required(): void
    {
        $response = $this->postJson('/api/products', [
            'name' => 'Test Product'
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors('price');
    }
}
