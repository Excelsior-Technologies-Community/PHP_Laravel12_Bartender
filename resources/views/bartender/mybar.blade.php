<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>🍺 My Bar</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { background: #0f172a; color: #e2e8f0; font-family: 'Segoe UI', sans-serif; }
        .glass { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.05); border-radius: 16px; }
        .ingredient-chip { transition: all 0.3s; cursor: pointer; }
        .ingredient-chip:hover { transform: scale(1.05); }
        .ingredient-chip.selected { background: #22c55e30; border-color: #22c55e; color: #22c55e; }
        .favorite-btn { transition: all 0.3s; cursor: pointer; }
        .favorite-btn:hover { transform: scale(1.2); }
        .progress-bar { transition: width 0.8s ease; }
    </style>
</head>
<body>

<div class="max-w-6xl mx-auto py-8 px-4">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-4xl font-bold">🍺 My Bar</h1>
            <p class="text-slate-400">Manage your ingredients and get personalized recommendations</p>
        </div>
        <a href="/" class="text-indigo-400 hover:text-indigo-300">← Back</a>
    </div>

    @if(session('success'))
        <div class="bg-green-500/20 border border-green-500 text-green-300 p-4 rounded-xl mb-6">{{ session('success') }}</div>
    @endif

    <div class="glass p-6 mb-8">
        <h2 class="text-2xl font-bold mb-4">🧪 Your Ingredients</h2>
        <p class="text-sm text-slate-400 mb-4">Select ingredients you have at home</p>

        <form action="{{ route('mybar.update') }}" method="POST">
            @csrf
            <div class="flex flex-wrap gap-3 mb-6">
                @foreach($ingredients as $ing)
                    <label class="ingredient-chip px-4 py-2 rounded-full border {{ in_array($ing->id, $userIngredients) ? 'selected border-green-500 bg-green-500/20' : 'border-slate-700 bg-slate-800' }} cursor-pointer transition">
                        <input type="checkbox" name="ingredients[]" value="{{ $ing->id }}" 
                               {{ in_array($ing->id, $userIngredients) ? 'checked' : '' }} class="hidden">
                        {{ $ing->icon ?? '🥃' }} {{ $ing->name }}
                    </label>
                @endforeach
            </div>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 px-6 py-3 rounded-xl font-bold transition">
                💾 Save My Bar
            </button>
        </form>
    </div>

    @if($favorites->count() > 0)
        <div class="glass p-6 mb-8">
            <h2 class="text-2xl font-bold mb-4">❤️ Your Favorite Drinks</h2>
            <div class="grid md:grid-cols-2 gap-4">
                @foreach($favorites as $drink)
                    <div class="bg-slate-800/50 p-4 rounded-xl flex justify-between items-center">
                        <div>
                            <h3 class="font-bold text-green-400">{{ $drink->name }}</h3>
                            <p class="text-sm text-slate-400">{{ $drink->description ?? 'No description' }}</p>
                            <div class="flex flex-wrap gap-1 mt-2">
                                @foreach($drink->ingredients as $ing)
                                    <span class="bg-slate-700 px-2 py-0.5 rounded-full text-xs">{{ $ing->name }}</span>
                                @endforeach
                            </div>
                        </div>
                        <button onclick="toggleFavorite({{ $drink->id }})" class="favorite-btn text-3xl" id="fav-{{ $drink->id }}">
                            ❤️
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if($recommendations->count() > 0)
        <div class="glass p-6">
            <h2 class="text-2xl font-bold mb-4">🎯 Drinks You Can Make</h2>
            <div class="grid md:grid-cols-2 gap-4">
                @foreach($recommendations as $rec)
                    <div class="bg-slate-800/50 p-4 rounded-xl">
                        <div class="flex justify-between items-start">
                            <h3 class="font-bold text-green-400">{{ $rec['drink']->name }}</h3>
                            <button onclick="toggleFavorite({{ $rec['drink']->id }})" class="favorite-btn text-2xl" id="fav-{{ $rec['drink']->id }}">
                                {{ auth()->user()->isFavorite($rec['drink']->id) ? '❤️' : '🤍' }}
                            </button>
                        </div>
                        <div class="mt-2">
                            <div class="flex justify-between text-sm">
                                <span>Match</span>
                                <span class="{{ $rec['percentage'] == 100 ? 'text-green-400' : 'text-yellow-400' }}">
                                    {{ $rec['percentage'] }}%
                                </span>
                            </div>
                            <div class="w-full bg-slate-700 rounded-full h-2">
                                <div class="progress-bar h-2 rounded-full {{ $rec['percentage'] == 100 ? 'bg-green-500' : 'bg-yellow-500' }}" 
                                     style="width: {{ $rec['percentage'] }}%"></div>
                            </div>
                            @if($rec['missing']->count() > 0)
                                <p class="text-xs text-slate-400 mt-1">
                                    Missing: {{ $rec['missing']->pluck('name')->join(', ') }}
                                </p>
                            @else
                                <span class="text-xs bg-green-500/20 text-green-300 px-2 py-0.5 rounded-full inline-block mt-1">✅ Ready to make!</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

<script>
    // Toggle ingredient selection visual
    document.querySelectorAll('.ingredient-chip').forEach(chip => {
        chip.addEventListener('click', function() {
            const checkbox = this.querySelector('input[type="checkbox"]');
            checkbox.checked = !checkbox.checked;
            this.classList.toggle('selected');
        });
    });

    function toggleFavorite(drinkId) {
        $.ajax({
            url: '/favorite/' + drinkId,
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                if (response.success) {
                    const btn = document.getElementById('fav-' + drinkId);
                    btn.textContent = response.is_favorite ? '❤️' : '🤍';
                    location.reload();
                }
            },
            error: function() {
                alert('Please login to add favorites');
            }
        });
    }
</script>

</body>
</html>