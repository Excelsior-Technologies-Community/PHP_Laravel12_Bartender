<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bartender</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #1e293b, #0f172a);
            color: #fff;
        }

        .container {
            max-width: 700px;
            margin: 50px auto;
            background: #111827;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.5);
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            font-weight: 600;
        }

        .ingredient {
            background: #1f2937;
            padding: 12px 15px;
            margin-bottom: 10px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            transition: 0.2s;
        }

        .ingredient:hover {
            background: #374151;
        }

        input[type="checkbox"] {
            margin-right: 10px;
            transform: scale(1.2);
        }

        button {
            width: 100%;
            padding: 12px;
            border: none;
            background: linear-gradient(90deg, #6366f1, #22c55e);
            color: white;
            font-size: 16px;
            border-radius: 10px;
            cursor: pointer;
            margin-top: 15px;
            transition: 0.3s;
        }

        button:hover {
            opacity: 0.9;
            transform: scale(1.02);
        }
    </style>
</head>

<body>

<div class="container">
    <h2>🍹 Select Ingredients</h2>

    <form method="POST" action="/find-drinks">
        @csrf

        @foreach($ingredients as $ingredient)
            <label class="ingredient">
                <input type="checkbox" name="ingredients[]" value="{{ $ingredient->id }}">
                {{ $ingredient->name }}
            </label>
        @endforeach

        <button type="submit">Find Drinks</button>
    </form>
</div>

</body>
</html>