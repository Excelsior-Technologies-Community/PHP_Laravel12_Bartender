<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Bartender Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-950 text-white min-h-screen">

<div class="max-w-6xl mx-auto py-10 px-5">

    <h1 class="text-4xl font-bold mb-8 text-center">
        🍹 Bartender Dashboard
    </h1>

    @if(session('success'))
        <div class="bg-green-500 text-white p-4 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    <!-- Search -->

    <form method="GET" action="/" class="mb-8">
        <input type="text"
               name="search"
               placeholder="Search drinks..."
               value="{{ request('search') }}"
               class="w-full p-4 rounded-xl bg-slate-800 border border-slate-700">
    </form>

    <!-- Add Drink -->

    <div class="bg-slate-900 p-6 rounded-2xl mb-10 shadow-lg">

        <h2 class="text-2xl font-semibold mb-5">
            ➕ Add New Drink
        </h2>

        <form action="/drinks" method="POST">

            @csrf

            <input type="text"
                   name="name"
                   placeholder="Drink Name"
                   class="w-full p-3 rounded-lg bg-slate-800 mb-4">

            <textarea name="description"
                      placeholder="Description"
                      class="w-full p-3 rounded-lg bg-slate-800 mb-4"></textarea>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-5">

                @foreach($ingredients as $ingredient)

                    <label class="bg-slate-800 p-3 rounded-lg flex items-center gap-2">

                        <input type="checkbox"
                               name="ingredients[]"
                               value="{{ $ingredient->id }}">

                        {{ $ingredient->name }}

                    </label>

                @endforeach

            </div>

            <button class="bg-indigo-600 hover:bg-indigo-700 px-6 py-3 rounded-xl w-full">
                Save Drink
            </button>

        </form>

    </div>

    <!-- Drinks List -->

    <div class="grid md:grid-cols-2 gap-6">

        @forelse($drinks as $drink)

            <div class="bg-slate-900 p-6 rounded-2xl shadow-lg">

                <div class="flex justify-between items-start">

                    <div>
                        <h2 class="text-2xl font-bold text-green-400">
                            {{ $drink->name }}
                        </h2>

                        <p class="text-slate-400 mt-2">
                            {{ $drink->description }}
                        </p>
                    </div>

                    <form action="/drinks/{{ $drink->id }}"
                          method="POST">

                        @csrf
                        @method('DELETE')

                        <button class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded-lg">
                            Delete
                        </button>

                    </form>

                </div>

                <div class="mt-5">

                    <h4 class="font-semibold mb-2">
                        Ingredients:
                    </h4>

                    <div class="flex flex-wrap gap-2">

                        @foreach($drink->ingredients as $ingredient)

                            <span class="bg-slate-800 px-3 py-1 rounded-full text-sm">
                                {{ $ingredient->name }}
                            </span>

                        @endforeach

                    </div>

                </div>

            </div>

        @empty

            <div class="col-span-2 text-center text-red-400 text-xl">
                ❌ No Drinks Found
            </div>

        @endforelse

    </div>

    <!-- Pagination -->

    <div class="mt-10">
        {{ $drinks->links() }}
    </div>

</div>

</body>
</html>