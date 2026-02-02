<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use PHPUnit\Framework\Attributes\Test;

class ProductBrowserTest extends DuskTestCase
{
    #[Test]
        public function user_can_submit_product_form(): void
        {
            $this->browse(function (Browser $browser) {
                $browser->visit('/product/create')
                        ->waitFor('input[name=name]')
                        ->type('input[name=name]', 'Dusk Product')
                        ->type('input[name=price]', '999')
                        ->press('Add Product')
                        ->waitForText('Product Added')
                        ->assertSee('Product Added');
            });
        }


}
