<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>🍸 Matching Drinks</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { background: #0f172a; color: #e2e8f0; font-family: 'Segoe UI', sans-serif; }
        .glass { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.05); border-radius: 16px; }
        .progress-bar { transition: width 0.8s ease; }
        .result-card { transition: all 0.3s; }
        .result-card:hover { transform: translateY(-4px); box-shadow: 0 8px 30px rgba(0,0,0,0.4); }
        .badge-complete { background: #22c55e20; color: #22c55e; border: 1px solid #22c55e40; }
        .badge-incomplete { background: #eab30820; color: #eab308; border: 1px solid #eab30840; }
        .favorite-btn { transition: all 0.3s; cursor: pointer; }
        .favorite-btn:hover { transform: scale(1.2); }
    </style>
</head>
<body>

<div class="max-w-4xl mx-auto py-10 px-4">
    <a href="/" class="text-indigo-400 hover:text-indigo-300 mb-6 inline-block">← Back to Dashboard</a>

    <h2 class="text-3xl font-bold mb-6">🍸 Matching Drinks</h2>

    @if($results->count())
        <div class="space-y-4">
            @foreach($results as $result)
                <div class="glass p-6 result-card">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-2xl font-bold text-green-400">{{ $result['drink']->name }}</h3>
                            <p class="text-slate-400 text-sm">{{ $result['drink']->description ?? 'No description' }}</p>
                        </div>
                        @auth
                            <button onclick="toggleFavorite({{ $result['drink']->id }})" class="favorite-btn text-3xl" id="fav-{{ $result['drink']->id }}">
                                {{ $result['is_favorite'] ? '❤️' : '🤍' }}
                            </button>
                        @endauth
                    </div>

                    <div class="mt-4">
                        <div class="flex justify-between text-sm mb-1">
                            <span>Match: {{ $result['percentage'] }}%</span>
                            @if($result['is_complete'])
                                <span class="badge-complete px-3 py-0.5 rounded-full text-xs">✅ Ready to make!</span>
                            @else
                                <span class="badge-incomplete px-3 py-0.5 rounded-full text-xs">⚠️ Missing ingredients</span>
                            @endif
                        </div>
                        <div class="w-full bg-slate-700 rounded-full h-3">
                            <div class="progress-bar h-3 rounded-full {{ $result['is_complete'] ? 'bg-green-500' : 'bg-yellow-500' }}" 
                                 style="width: {{ $result['percentage'] }}%"></div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <p class="text-sm font-semibold text-slate-300">Ingredients:</p>
                        <div class="flex flex-wrap gap-2 mt-1">
                            @foreach($result['drink']->ingredients as $ing)
                                <span class="{{ in_array($ing->id, $ingredientIds ?? []) ? 'bg-green-500/20 text-green-300' : 'bg-red-500/20 text-red-300' }} 
                                       px-3 py-1 rounded-full text-xs">
                                    {{ $ing->name }}
                                    @if(!in_array($ing->id, $ingredientIds ?? []))
                                        ❌
                                    @else
                                        ✅
                                    @endif
                                </span>
                            @endforeach
                        </div>
                    </div>

                    @if($result['missing']->count() > 0)
                        <div class="mt-3 p-3 bg-yellow-500/10 rounded-lg border border-yellow-500/20">
                            <p class="text-sm text-yellow-400">
                                ⚠️ Missing: {{ $result['missing']->pluck('name')->join(', ') }}
                            </p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="glass p-12 text-center">
            <div class="text-6xl mb-4">🍹</div>
            <h3 class="text-2xl font-bold text-slate-300">No Matching Drinks Found</h3>
            <p class="text-slate-500 mt-2">Try selecting different ingredients</p>
            <a href="/" class="inline-block mt-4 bg-indigo-600 hover:bg-indigo-700 px-6 py-3 rounded-xl transition">Go Back</a>
        </div>
    @endif
</div>

<div id="toast" class="fixed bottom-6 right-6 z-50" style="display:none;"></div>

<script>
    function showToast(message, type = 'info') {
        const toast = document.getElementById('toast');
        toast.textContent = message;
        toast.className = 'toast-' + type + ' fixed bottom-6 right-6 z-50 px-6 py-3 rounded-xl shadow-lg transition-all duration-300';
        if (type === 'success') {
            toast.className += ' bg-green-500/20 border border-green-500 text-green-300';
        } else if (type === 'error') {
            toast.className += ' bg-red-500/20 border border-red-500 text-red-300';
        } else {
            toast.className += ' bg-indigo-500/20 border border-indigo-500 text-indigo-300';
        }
        toast.style.display = 'block';
        setTimeout(() => {
            toast.style.display = 'none';
        }, 3000);
    }

    function toggleFavorite(drinkId) {
        $.ajax({
            url: '/favorite/' + drinkId,
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                if (response.success) {
                    const btn = document.getElementById('fav-' + drinkId);
                    btn.textContent = response.is_favorite ? '❤️' : '🤍';
                    showToast(response.message, 'success');
                }
            },
            error: function() {
                showToast('❌ Please login to add favorites', 'error');
            }
        });
    }
</script>

</body>
</html>