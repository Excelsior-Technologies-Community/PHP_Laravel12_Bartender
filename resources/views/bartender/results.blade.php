<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Results</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #0f172a, #020617);
            color: #fff;
        }

        .container {
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        .card {
            background: #111827;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 14px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.4);
            transition: 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card h3 {
            margin: 0;
            color: #22c55e;
        }

        .card p {
            color: #9ca3af;
        }

        ul {
            padding-left: 20px;
        }

        li {
            color: #e5e7eb;
        }

        .empty {
            text-align: center;
            color: #f87171;
        }

        .back {
            display: inline-block;
            margin-bottom: 20px;
            color: #60a5fa;
            text-decoration: none;
        }
    </style>
</head>

<body>

<div class="container">

    <a href="/" class="back">← Back</a>

    <h2>🍸 Matching Drinks</h2>

    @if($drinks->count())
        @foreach($drinks as $drink)
            <div class="card">
                <h3>{{ $drink->name }}</h3>
                <p>{{ $drink->description }}</p>

                <strong>Ingredients:</strong>
                <ul>
                    @foreach($drink->ingredients as $ingredient)
                        <li>{{ $ingredient->name }}</li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    @else
        <p class="empty">❌ No matching drinks found</p>
    @endif

</div>

</body>
</html>