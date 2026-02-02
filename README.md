# PHP_Laravel12_Automated_Testing

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12-red" alt="Laravel 12">
  <img src="https://img.shields.io/badge/Testing-PHPUnit-blue" alt="PHPUnit Testing">
  <img src="https://img.shields.io/badge/Browser%20Testing-Dusk-purple" alt="Laravel Dusk">
  <img src="https://img.shields.io/badge/Database-MySQL-orange" alt="MySQL">
  <img src="https://img.shields.io/badge/Status-Completed-brightgreen" alt="Project Status">
</p>

---

##  Overview

This project demonstrates **Automated Testing**, **Test‑Driven Development (TDD)**, and **Browser Automation using Laravel Dusk** in Laravel 12.

It covers the complete testing workflow including API testing, database assertions, validation testing, and end‑to‑end browser testing. The goal is to show how modern Laravel applications can be built with confidence using automated tests from backend logic to real user interactions in the browser.

---

##  Features

* Product API (Create & List)
* Automated API testing using PHPUnit
* Database testing with factories
* Validation testing for required fields
* TDD workflow (Red → Green → Refactor)
* Web product form with styled UI
* End‑to‑end browser automation using Laravel Dusk

---

##  Folder Structure

```
laravel-testing/
│
├── app/
│ ├── Models/
│ │ └── Product.php
│ └── Http/
│ └── Controllers/
│ └── Api/
│ └── ProductController.php
│
├── database/
│ ├── factories/
│ │ └── ProductFactory.php
│ └── migrations/
│ └── xxxx_create_products_table.php
│
├── resources/
│ └── views/
│ └── product-form.blade.php
│
├── routes/
│ ├── api.php
│ └── web.php
│
├── tests/
│ ├── Feature/
│ │ └── ProductApiTest.php
│ └── Browser/
│ └── ProductBrowserTest.php
│
└── .env.dusk.local
```

---

## STEP 1 — Install Laravel Project

```bash
composer create-project laravel/laravel laravel-testing
```

---

## STEP 2 — Database Setup

Open `.env` and update:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=testing
DB_USERNAME=root
DB_PASSWORD=
```

Run migrations:

```bash
php artisan migrate
```

---

## STEP 3 — Confirm Testing Works

```bash
php artisan test
```

---

## STEP 4 — TDD: Write Test First

```bash
php artisan make:test ProductApiTest
```

### tests/Feature/ProductApiTest.php

```php
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
                 ->assertJsonFragment(['name' => 'iPhone 15']);

        $this->assertDatabaseHas('products', ['name' => 'iPhone 15']);
    }

    #[Test]
    public function product_name_is_required(): void
    {
        $response = $this->postJson('/api/products', ['price' => 500]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors('name');
    }

    #[Test]
    public function product_price_is_required(): void
    {
        $response = $this->postJson('/api/products', ['name' => 'Test Product']);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors('price');
    }
}
```

Run test → it will FAIL (this is expected in TDD)

---

## STEP 5 — Create Model + Migration + Factory

```bash
php artisan make:model Product -mf
```

### app/Models/Product.php

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'price'];
}
```

### database/migrations/xxxx_create_products_table.php

```php
public function up()
{
    Schema::create('products', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->integer('price');
        $table->timestamps();
    });
}
```

Run:

```bash
php artisan migrate
```

### database/factories/ProductFactory.php

```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'price' => fake()->numberBetween(100, 1000),
        ];
    }
}
```

---

## STEP 6 — Create API Controller

```bash
php artisan make:controller Api/ProductController
```

### app/Http/Controllers/Api/ProductController.php

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return response()->json(Product::all(), 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'price' => 'required|integer',
        ]);

        $product = Product::create($validated);

        return response()->json($product, 201);
    }
}
```

---

## STEP 7 — Add API Routes

### routes/api.php

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;

Route::get('/products', [ProductController::class, 'index']);
Route::post('/products', [ProductController::class, 'store']);

```

### Enable API routes in bootstrap/app.php

```php
->withRouting(
    web: __DIR__.'/../routes/web.php',
    api: __DIR__.'/../routes/api.php',
    commands: __DIR__.'/../routes/console.php',
    health: '/up',
)
```

Run tests again:

```bash
php artisan test
```

Tests should now PASS.

---

## STEP 8 — Create Web Form

### routes/web.php

```php
use App\Models\Product;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/product/create', function () {
    return view('product-form');
});

Route::post('/product/store', function (Request $request) {
    Product::create($request->validate([
        'name' => 'required',
        'price' => 'required|integer'
    ]));

    return redirect('/product/create')->with('success', 'Product Added');
});
```

### resources/views/product-form.blade.php

```html
<!DOCTYPE html>
<html>
<head>
    <title>Product Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .card {
            background: white;
            padding: 30px 35px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            width: 320px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        .success {
            background: #e6ffed;
            color: #1a7f37;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            font-size: 14px;
            text-align: center;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 12px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 14px;
            transition: border 0.2s ease;
        }

        input:focus {
            border-color: #4f46e5;
            outline: none;
        }

        button {
            width: 100%;
            padding: 10px;
            background: #4f46e5;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        button:hover {
            background: #4338ca;
        }
    </style>
</head>
<body>

<div class="card">
    <h2>Add Product</h2>

    @if(session('success'))
        <div class="success" id="success-msg">{{ session('success') }}</div>
    @endif

    <form method="POST" action="/product/store">
        @csrf
        <input type="text" name="name" placeholder="Product Name" dusk="name-input">
        <input type="number" name="price" placeholder="Price" dusk="price-input">
        <button type="submit" dusk="submit-btn">Add Product</button>
    </form>
</div>

</body>
</html>

```
<img width="529" height="393" alt="Screenshot 2026-02-02 125843" src="https://github.com/user-attachments/assets/e7e742b4-9ead-4461-ba20-3bff5f2fe829" />

---

## STEP 9 — Install Laravel Dusk

```bash
composer require --dev laravel/dusk

php artisan dusk:install
```

---

## STEP 10 — Configure Dusk

Create `.env.dusk.local`

```
APP_KEY=copy_from_env
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_DATABASE=testing
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
```

Run migrations for Dusk DB:

```bash
php artisan migrate --env=dusk.local
```

---

## STEP 11 — Dusk Browser Test

### tests/Browser/ProductBrowserTest.php

```php
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
                    ->type('input[name=name]', 'Dusk Product')
                    ->type('input[name=price]', '999')
                    ->press('Add Product')
                    ->waitForText('Product Added')
                    ->assertSee('Product Added');
        });
    }
}
```

---

## STEP 12 — Run Everything

**Start Laravel development server:**

```bash
php artisan serve
```

**Run API (PHPUnit) tests:**

```bash
php artisan test
```
<img width="542" height="327" alt="Screenshot 2026-02-02 130745" src="https://github.com/user-attachments/assets/6ec51b37-768b-4871-b0fd-9ea8f8be4de8" />


**Run browser automation tests (Dusk):**

```bash
php artisan dusk
```
<img width="747" height="288" alt="Screenshot 2026-02-02 125736" src="https://github.com/user-attachments/assets/ab92834f-1b98-40f8-a4f2-10d53e8c256c" />

---

##  Database

<img width="688" height="230" alt="Screenshot 2026-02-02 125754" src="https://github.com/user-attachments/assets/30eec05d-e515-44b8-8c52-00df83f2d62b" />


##  API Endpoints

| Method | Endpoint      | Description          |
| ------ | ------------- | -------------------- |
| GET    | /api/products | Fetch all products   |
| POST   | /api/products | Create a new product |

---
<img width="1012" height="148" alt="Screenshot 2026-02-02 130934" src="https://github.com/user-attachments/assets/c0c9bb42-26ff-4f39-8c6c-2bd2a142803d" />


# FINAL RESULT

You now have a Laravel 12 project with:

* Automated API Testing
* TDD Workflow
* Validation Testing
* Database Assertions
* Web Form
* Real Browser Automation using Dusk
