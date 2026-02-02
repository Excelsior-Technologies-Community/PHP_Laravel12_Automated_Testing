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
