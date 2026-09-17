<?php

namespace Tests\Browser;

use App\Models\Product;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;

class ProductBrowserTest extends DuskTestCase
{
    /*
    |--------------------------------------------------------------------------
    | FORM DISPLAY
    |--------------------------------------------------------------------------
    */

    #[Test]
    public function product_form_is_displayed(): void
    {
        $this->browse(function (Browser $browser) {

            $browser->visit('/product/create')

                ->assertSee('Add Product')

                ->assertPresent('@name-input')
                ->assertPresent('@price-input')
                ->assertPresent('@submit-btn');

        });
    }

    /*
    |--------------------------------------------------------------------------
    | SUCCESSFUL PRODUCT CREATION
    |--------------------------------------------------------------------------
    */

    #[Test]
    public function user_can_submit_product_form(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Clear existing products so the unique name validation
        | does not interfere with this test.
        |--------------------------------------------------------------------------
        */

        Product::query()->delete();

        $this->browse(function (Browser $browser) {

            $browser->visit('/product/create')

                ->type('@name-input', 'Dusk Product')

                ->type('@price-input', '999')

                ->press('@submit-btn')

                ->waitForText('Product Added')

                ->assertSee('Product Added');

        });

        $this->assertDatabaseHas('products', [
            'name' => 'Dusk Product',
            'price' => 999,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDATION TEST
    |--------------------------------------------------------------------------
    */

    #[Test]
    public function product_form_shows_validation_errors(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Clear existing products so this test starts with a clean database.
        |--------------------------------------------------------------------------
        */

        Product::query()->delete();

        $this->browse(function (Browser $browser) {

            $browser->visit('/product/create')

                ->press('@submit-btn')

                ->waitFor('@validation-errors')

                ->assertPresent('@validation-errors')

                ->assertSee('The name field is required.')

                ->assertSee('The price field is required.');

        });
    }

    /*
    |--------------------------------------------------------------------------
    | INVALID PRICE TEST
    |--------------------------------------------------------------------------
    */

    #[Test]
    public function product_form_rejects_invalid_price(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Clear existing products so the test is independent.
        |--------------------------------------------------------------------------
        */

        Product::query()->delete();

        $this->browse(function (Browser $browser) {

            $browser->visit('/product/create')

                ->type('@name-input', 'Invalid Price Product')

                ->type('@price-input', '0')

                ->press('@submit-btn')

                ->waitFor('@validation-errors')

                ->assertPresent('@validation-errors')

                ->assertSee('The price field must be at least 1.');

        });
    }

    /*
    |--------------------------------------------------------------------------
    | PRODUCT LISTING
    |--------------------------------------------------------------------------
    */

    #[Test]
    public function products_are_displayed_in_product_list(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Clear existing products so this test is independent.
        |--------------------------------------------------------------------------
        */

        Product::query()->delete();

        Product::create([
            'name' => 'Dusk Listing Product',
            'price' => 2500,
        ]);

        $this->browse(function (Browser $browser) {

            $browser->visit('/products')

                ->assertSee('Product List')

                ->assertSee('Dusk Listing Product')

                ->assertSee('₹2,500')

                ->assertPresent('@products-table');

        });
    }

    /*
    |--------------------------------------------------------------------------
    | PRODUCT COUNT
    |--------------------------------------------------------------------------
    */

    #[Test]
    public function product_count_is_displayed(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Clear existing products first.
        | This guarantees exactly 3 products exist.
        |--------------------------------------------------------------------------
        */

        Product::query()->delete();

        Product::factory()->count(3)->create();

        $this->browse(function (Browser $browser) {

            $browser->visit('/products')

                ->assertSee('Total Products: 3')

                ->assertPresent('@product-count');

        });
    }

    /*
    |--------------------------------------------------------------------------
    | NAVIGATION TEST
    |--------------------------------------------------------------------------
    */

    #[Test]
    public function user_can_navigate_from_product_list_to_create_form(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Clear existing products so the navigation test starts clean.
        |--------------------------------------------------------------------------
        */

        Product::query()->delete();

        $this->browse(function (Browser $browser) {

            $browser->visit('/products')

                ->click('@add-product-link')

                ->waitForLocation('/product/create')

                ->assertPathIs('/product/create')

                ->assertSee('Add Product');

        });
    }
}