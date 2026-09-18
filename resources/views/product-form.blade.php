<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Add Product</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .card {
            background: white;
            padding: 30px 35px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            width: 420px;
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

        .error {
            background: #ffe6e6;
            color: #b42318;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .error ul {
            margin: 0;
            padding-left: 20px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-size: 14px;
            font-weight: bold;
            color: #444;
        }

        input {
            width: 100%;
            padding: 11px;
            margin-bottom: 14px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 14px;
        }

        input:focus {
            border-color: #4f46e5;
            outline: none;
        }

        button {
            width: 100%;
            padding: 11px;
            background: #4f46e5;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            cursor: pointer;
        }

        button:hover {
            background: #4338ca;
        }

        .link {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #4f46e5;
            text-decoration: none;
        }

    </style>

</head>

<body>

<div class="card">

    <h2>Add Product</h2>


    @if(session('success'))

        <div
            class="success"
            id="success-msg"
            dusk="success-message"
        >
            {{ session('success') }}
        </div>

    @endif


    @if($errors->any())

        <div
            class="error"
            id="validation-errors"
            dusk="validation-errors"
        >

            <strong>
                Please fix the following errors:
            </strong>

            <ul>

                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    <form
        method="POST"
        action="/product/store"
        dusk="product-form"
    >

        @csrf


        <label for="name">
            Product Name
        </label>

        <input
            id="name"
            type="text"
            name="name"
            value="{{ old('name') }}"
            placeholder="Product Name"
            dusk="name-input"
        >


        <label for="price">
            Product Price
        </label>

        <input
            id="price"
            type="number"
            name="price"
            value="{{ old('price') }}"
            placeholder="Price"
            min="1"
            dusk="price-input"
        >


        <button
            type="submit"
            dusk="submit-btn"
        >
            Add Product
        </button>

    </form>


    <a
        href="/products"
        class="link"
        dusk="products-link"
    >
        View Products
    </a>

</div>

</body>

</html>