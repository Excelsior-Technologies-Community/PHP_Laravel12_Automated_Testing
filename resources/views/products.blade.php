<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <title>Product List</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 40px;
        }

        .container {
            max-width: 900px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        }

        h1 {
            margin-top: 0;
            color: #333;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .count {
            font-weight: bold;
            color: #4f46e5;
        }

        .add-btn {
            background: #4f46e5;
            color: white;
            padding: 10px 15px;
            border-radius: 6px;
            text-decoration: none;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #f4f6f9;
        }

        .empty {
            text-align: center;
            padding: 30px;
            color: #777;
        }
    </style>
</head>

<body>

<div class="container">

    <div class="top-bar">

        <div>
            <h1>Product List</h1>

            <div
                class="count"
                dusk="product-count"
            >
                Total Products: {{ $products->count() }}
            </div>
        </div>

        <a
            href="/product/create"
            class="add-btn"
            dusk="add-product-link"
        >
            Add New Product
        </a>

    </div>

    @if($products->count())

        <table dusk="products-table">

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Price</th>
                </tr>
            </thead>

            <tbody>

                @foreach($products as $product)

                    <tr dusk="product-row-{{ $product->id }}">

                        <td>
                            {{ $product->id }}
                        </td>

                        <td>
                            {{ $product->name }}
                        </td>

                        <td>
                            ₹{{ number_format($product->price) }}
                        </td>

                    </tr>

                @endforeach

            </tbody>

        </table>

    @else

        <div
            class="empty"
            dusk="empty-products"
        >
            No products found.
        </div>

    @endif

</div>

</body>
</html>