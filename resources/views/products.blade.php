<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Product Management</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 30px;
        }

        .container {
            max-width: 1200px;
            margin: auto;
        }

        .header {
            background: white;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }

        h1 {
            margin: 0 0 8px;
            color: #222;
        }

        .subtitle {
            color: #777;
            margin: 0;
        }

        .add-btn {
            background: #4f46e5;
            color: white;
            padding: 11px 18px;
            border-radius: 7px;
            text-decoration: none;
            font-weight: bold;
        }

        .add-btn:hover {
            background: #4338ca;
        }

        /*
        |--------------------------------------------------------------------------
        | Statistics
        |--------------------------------------------------------------------------
        */

        .stats {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
        }

        .stat-label {
            color: #777;
            font-size: 13px;
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 23px;
            font-weight: bold;
            color: #4f46e5;
        }

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        .filter-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
        }

        .filter-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr 1fr auto;
            gap: 10px;
            align-items: end;
        }

        .field label {
            display: block;
            font-size: 13px;
            font-weight: bold;
            color: #444;
            margin-bottom: 6px;
        }

        .field input,
        .field select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }

        .filter-btn {
            padding: 10px 16px;
            background: #4f46e5;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .clear-btn {
            display: inline-block;
            padding: 10px 16px;
            background: #e5e7eb;
            color: #333;
            border-radius: 6px;
            text-decoration: none;
            margin-top: 10px;
        }

        /*
        |--------------------------------------------------------------------------
        | Messages
        |--------------------------------------------------------------------------
        */

        .success {
            background: #e6ffed;
            color: #1a7f37;
            padding: 12px;
            border-radius: 7px;
            margin-bottom: 20px;
        }

        /*
        |--------------------------------------------------------------------------
        | Table
        |--------------------------------------------------------------------------
        */

        .table-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
        }

        .table-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .count {
            font-weight: bold;
            color: #4f46e5;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 13px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
        }

        th {
            background: #f8fafc;
            color: #374151;
        }

        tr:hover {
            background: #fafafa;
        }

        .price {
            font-weight: bold;
            color: #111827;
        }

        .delete-btn {
            background: #dc2626;
            color: white;
            border: none;
            padding: 7px 12px;
            border-radius: 5px;
            cursor: pointer;
        }

        .delete-btn:hover {
            background: #b91c1c;
        }

        .empty {
            text-align: center;
            padding: 40px;
            color: #777;
        }

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        .pagination-wrapper {
            margin-top: 20px;
            display: flex;
            justify-content: center;
        }

        .pagination {
            display: flex;
            gap: 6px;
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .pagination li a,
        .pagination li span {
            display: block;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-decoration: none;
            color: #4f46e5;
        }

        .pagination li.active span {
            background: #4f46e5;
            color: white;
            border-color: #4f46e5;
        }

        /*
        |--------------------------------------------------------------------------
        | Responsive
        |--------------------------------------------------------------------------
        */

        @media (max-width: 1000px) {

            .stats {
                grid-template-columns: repeat(2, 1fr);
            }

            .filter-grid {
                grid-template-columns: 1fr 1fr;
            }

        }

        @media (max-width: 600px) {

            body {
                padding: 15px;
            }

            .header-top {
                flex-direction: column;
                align-items: flex-start;
            }

            .stats {
                grid-template-columns: 1fr;
            }

            .filter-grid {
                grid-template-columns: 1fr;
            }

            .table-card {
                overflow-x: auto;
            }

        }

    </style>

</head>

<body>

<div class="container">

    {{-- Header --}}

    <div class="header">

        <div class="header-top">

            <div>

                <h1>Product Management</h1>

                <p class="subtitle">
                    Search, filter, sort and manage products.
                </p>

            </div>

            <a
                href="/product/create"
                class="add-btn"
                dusk="add-product-link"
            >
                + Add Product
            </a>

        </div>

    </div>


    {{-- Success Message --}}

    @if(session('success'))

        <div
            class="success"
            id="success-message"
            dusk="success-message"
        >
            {{ session('success') }}
        </div>

    @endif


    {{-- Statistics --}}

    <div class="stats">

        <div class="stat-card">

            <div class="stat-label">
                Total Products
            </div>

            <div
                class="stat-value"
                dusk="product-count"
            >
                {{ $totalProducts }}
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-label">
                Total Inventory Value
            </div>

            <div class="stat-value">
                ₹{{ number_format($totalValue) }}
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-label">
                Average Price
            </div>

            <div class="stat-value">
                ₹{{ number_format($averagePrice ?? 0, 2) }}
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-label">
                Minimum Price
            </div>

            <div class="stat-value">
                ₹{{ number_format($minimumPrice ?? 0) }}
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-label">
                Maximum Price
            </div>

            <div class="stat-value">
                ₹{{ number_format($maximumPrice ?? 0) }}
            </div>

        </div>

    </div>


    {{-- Filters --}}

    <div class="filter-card">

        <form
            method="GET"
            action="/products"
            dusk="product-filter-form"
        >

            <div class="filter-grid">

                <div class="field">

                    <label for="search">
                        Search
                    </label>

                    <input
                        id="search"
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search product..."
                        dusk="search-input"
                    >

                </div>


                <div class="field">

                    <label for="min_price">
                        Min Price
                    </label>

                    <input
                        id="min_price"
                        type="number"
                        name="min_price"
                        value="{{ request('min_price') }}"
                        min="1"
                        placeholder="Minimum"
                    >

                </div>


                <div class="field">

                    <label for="max_price">
                        Max Price
                    </label>

                    <input
                        id="max_price"
                        type="number"
                        name="max_price"
                        value="{{ request('max_price') }}"
                        min="1"
                        placeholder="Maximum"
                    >

                </div>


                <div class="field">

                    <label for="sort_by">
                        Sort By
                    </label>

                    <select
                        id="sort_by"
                        name="sort_by"
                    >

                        <option
                            value="id"
                            {{ $sortBy === 'id' ? 'selected' : '' }}
                        >
                            ID
                        </option>

                        <option
                            value="name"
                            {{ $sortBy === 'name' ? 'selected' : '' }}
                        >
                            Name
                        </option>

                        <option
                            value="price"
                            {{ $sortBy === 'price' ? 'selected' : '' }}
                        >
                            Price
                        </option>

                        <option
                            value="created_at"
                            {{ $sortBy === 'created_at' ? 'selected' : '' }}
                        >
                            Created Date
                        </option>

                    </select>

                </div>


                <div class="field">

                    <label for="sort_direction">
                        Direction
                    </label>

                    <select
                        id="sort_direction"
                        name="sort_direction"
                    >

                        <option
                            value="asc"
                            {{ $sortDirection === 'asc' ? 'selected' : '' }}
                        >
                            Ascending
                        </option>

                        <option
                            value="desc"
                            {{ $sortDirection === 'desc' ? 'selected' : '' }}
                        >
                            Descending
                        </option>

                    </select>

                </div>


                <div>

                    <button
                        type="submit"
                        class="filter-btn"
                        dusk="filter-btn"
                    >
                        Apply
                    </button>

                </div>

            </div>


            @if(
                request()->filled('search') ||
                request()->filled('min_price') ||
                request()->filled('max_price')
            )

                <a
                    href="/products"
                    class="clear-btn"
                    dusk="clear-filters"
                >
                    Clear Filters
                </a>

            @endif

        </form>

    </div>


    {{-- Product Table --}}

    <div class="table-card">

        <div class="table-top">

            <div class="count">

                Showing
                {{ $products->firstItem() ?? 0 }}
                -
                {{ $products->lastItem() ?? 0 }}
                of
                {{ $products->total() }}
                products

            </div>

        </div>


        @if($products->count())

            <table dusk="products-table">

                <thead>

                    <tr>

                        <th>ID</th>

                        <th>Name</th>

                        <th>Price</th>

                        <th>Created</th>

                        <th>Action</th>

                    </tr>

                </thead>


                <tbody>

                    @foreach($products as $product)

                        <tr
                            dusk="product-row-{{ $product->id }}"
                        >

                            <td>
                                {{ $product->id }}
                            </td>

                            <td>
                                {{ $product->name }}
                            </td>

                            <td class="price">
                                ₹{{ number_format($product->price) }}
                            </td>

                            <td>
                                {{ $product->created_at?->format('d M Y') }}
                            </td>

                            <td>

                                <form
                                    method="POST"
                                    action="/product/{{ $product->id }}"
                                    onsubmit="return confirm('Are you sure you want to delete this product?');"
                                    style="display:inline;"
                                >

                                    @csrf

                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="delete-btn"
                                        dusk="delete-product-{{ $product->id }}"
                                    >
                                        Delete
                                    </button>

                                </form>

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>


            {{-- Pagination --}}

            @if($products->hasPages())

                <div
                    class="pagination-wrapper"
                    dusk="pagination"
                >

                    {{ $products->links() }}

                </div>

            @endif


        @else

            <div
                class="empty"
                dusk="empty-products"
            >

                No products found.

            </div>

        @endif

    </div>

</div>

</body>

</html>