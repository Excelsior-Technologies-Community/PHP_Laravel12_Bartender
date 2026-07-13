<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>🍹 Bartender Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { background: #0f172a; color: #e2e8f0; font-family: 'Segoe UI', sans-serif; }
        .glass { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.05); border-radius: 16px; }
        .favorite-btn { transition: all 0.3s; cursor: pointer; }
        .favorite-btn:hover { transform: scale(1.2); }
        .progress-bar { transition: width 0.8s ease; }
        .drink-card { transition: all 0.3s; }
        .drink-card:hover { transform: translateY(-4px); box-shadow: 0 8px 30px rgba(0,0,0,0.4); }
        .toast { position: fixed; bottom: 30px; right: 30px; padding: 14px 24px; border-radius: 12px; background: rgba(30,41,59,0.95); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.1); color: white; font-size: 14px; z-index: 9999; transform: translateY(20px); opacity: 0; transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55); max-width: 400px; }
        .toast.show { opacity: 1; transform: translateY(0); }
        .toast.success { border-left: 4px solid #22c55e; }
        .toast.error { border-left: 4px solid #ef4444; }
        .badge-complete { background: #22c55e20; color: #22c55e; border: 1px solid #22c55e40; }
        .badge-incomplete { background: #eab30820; color: #eab308; border: 1px solid #eab30840; }
    </style>
</head>
<body>

<div class="max-w-7xl mx-auto py-8 px-4">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-4xl font-bold">🍹 Bartender Dashboard</h1>
        <div class="flex gap-3">
            @auth
                <a href="{{ route('mybar') }}" class="bg-indigo-600 hover:bg-indigo-700 px-5 py-2 rounded-xl transition">
                    🍺 My Bar
                </a>
                <span class="text-slate-400 flex items-center">👋 {{ Auth::user()->name }}</span>
                <a href="{{ route('logout') }}" class="text-red-400 hover:text-red-300">Logout</a>
            @else
                <a href="{{ route('login') }}" class="bg-indigo-600 hover:bg-indigo-700 px-5 py-2 rounded-xl transition">Login</a>
                <a href="{{ route('register') }}" class="bg-slate-700 hover:bg-slate-600 px-5 py-2 rounded-xl transition">Register</a>
            @endauth
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-500/20 border border-green-500 text-green-300 p-4 rounded-xl mb-6">{{ session('success') }}</div>
    @endif

    @if($recommendations->count() > 0 && auth()->check())
        <div class="glass p-6 mb-8">
            <h2 class="text-2xl font-bold mb-4">🎯 Recommended For You</h2>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($recommendations as $rec)
                    <div class="bg-slate-800/50 p-4 rounded-xl">
                        <div class="flex justify-between items-start">
                            <h3 class="font-bold text-green-400">{{ $rec['drink']->name }}</h3>
                            @auth
                                <button onclick="toggleFavorite({{ $rec['drink']->id }})" class="favorite-btn text-2xl" id="fav-{{ $rec['drink']->id }}">
                                    {{ auth()->user()->isFavorite($rec['drink']->id) ? '❤️' : '🤍' }}
                                </button>
                            @endauth
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
                                <span class="text-xs badge-complete px-2 py-0.5 rounded-full inline-block mt-1">✅ Ready to make!</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if($popularDrinks->count() > 0)
        <div class="glass p-6 mb-8">
            <h2 class="text-2xl font-bold mb-4">🔥 Most Popular</h2>
            <div class="flex flex-wrap gap-3">
                @foreach($popularDrinks as $drink)
                    <span class="bg-slate-800 px-4 py-2 rounded-full text-sm">
                        {{ $drink->name }} ❤️ {{ $drink->favorite_count }}
                    </span>
                @endforeach
            </div>
        </div>
    @endif

    <form method="GET" action="/" class="mb-8">
        <input type="text" name="search" placeholder="🔍 Search drinks..." value="{{ request('search') }}" 
               class="w-full p-4 rounded-xl bg-slate-800 border border-slate-700 focus:ring-2 focus:ring-indigo-500 outline-none">
    </form>

    <div class="grid lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <div class="grid md:grid-cols-2 gap-4">
                @forelse($drinks as $drink)
                    <div class="drink-card bg-slate-800 p-5 rounded-xl">
                        <div class="flex justify-between items-start">
                            <h3 class="text-xl font-bold text-green-400">{{ $drink->name }}</h3>
                            <div class="flex gap-2">
                                @auth
                                    <button onclick="toggleFavorite({{ $drink->id }})" class="favorite-btn text-2xl" id="fav-{{ $drink->id }}">
                                        {{ auth()->user()->isFavorite($drink->id) ? '❤️' : '🤍' }}
                                    </button>
                                @endauth
                                <form action="/drinks/{{ $drink->id }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button class="text-red-400 hover:text-red-300">🗑️</button>
                                </form>
                            </div>
                        </div>
                        <p class="text-slate-400 text-sm mt-1">{{ $drink->description ?? 'No description' }}</p>
                        <div class="mt-3 flex flex-wrap gap-1">
                            @foreach($drink->ingredients as $ing)
                                <span class="bg-slate-700 px-2 py-0.5 rounded-full text-xs">{{ $ing->name }}</span>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="col-span-2 text-center text-red-400 py-10">❌ No drinks found</div>
                @endforelse
            </div>
            <div class="mt-6">{{ $drinks->links() }}</div>
        </div>

        <div class="glass p-6">
            <h2 class="text-2xl font-bold mb-4">🔍 Find Drinks</h2>
            <p class="text-sm text-slate-400 mb-4">Select ingredients you have and find matching drinks</p>
            <form action="{{ route('find.drinks') }}" method="POST">
                @csrf
                <div class="space-y-2 max-h-60 overflow-y-auto">
                    @foreach($ingredients as $ing)
                        <label class="flex items-center gap-2 bg-slate-800 p-2 rounded-lg hover:bg-slate-700 cursor-pointer">
                            <input type="checkbox" name="ingredients[]" value="{{ $ing->id }}" class="w-4 h-4">
                            <span>{{ $ing->icon ?? '🥃' }} {{ $ing->name }}</span>
                        </label>
                    @endforeach
                </div>
                <button type="submit" class="w-full mt-4 bg-indigo-600 hover:bg-indigo-700 py-3 rounded-xl font-bold transition">
                    🍸 Find Drinks
                </button>
            </form>
        </div>
    </div>
</div>

<div id="toast" class="toast"></div>

<script>
    function showToast(message, type = 'info') {
        const toast = document.getElementById('toast');
        toast.textContent = message;
        toast.className = 'toast ' + type;
        setTimeout(() => toast.classList.add('show'), 50);
        clearTimeout(toast._timer);
        toast._timer = setTimeout(() => toast.classList.remove('show'), 3000);
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