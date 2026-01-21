<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Products') }}
            </h2>
            <a href="{{ route('products.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Create Product
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if ($errors->has('search'))
                <div class="mb-4 text-red-600 font-medium">
                    {{ $errors->first('search') }}
                </div>
            @endif

            {{-- Search & Filter Form --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('products.index') }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="keyword" class="block text-sm font-medium text-gray-700">Search</label>
                                <input type="text" name="keyword" id="keyword" value="{{ request('keyword') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                    placeholder="Search by product title or description...">
                            </div>
                            <div>
                                <label for="min_price" class="block text-sm font-medium text-gray-700">Min Price</label>
                                <input type="number" name="min_price" id="min_price" value="{{ request('min_price') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" step="0.01">
                            </div>
                            <div>
                                <label for="max_price" class="block text-sm font-medium text-gray-700">Max Price</label>
                                <input type="number" name="max_price" id="max_price" value="{{ request('max_price') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" step="0.01">
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Search
                            </button>
                            <a href="{{ route('products.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Products List --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($products->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($products as $product)
                        <div class="border rounded-lg p-4 hover:shadow-lg transition">
                            <h3 class="text-lg font-semibold mb-2">
                                <a href="{{ route('products.show', $product) }}" class="text-blue-600 hover:underline">
                                    {{ $product->title }}
                                </a>
                            </h3>
                            <p title="{{ $product->description }}" class="text-gray-600 mb-2 line-clamp-2">
                                {!! Str::limit(strip_tags($product->description), 100) !!}
                            </p>
                            <p class="text-xl font-bold text-green-600 mb-2">
                                ${{ number_format($product->price, 2) }}
                            </p>
                            <p class="text-sm text-gray-500 mb-4">
                                Available: {{ $product->date_available->format('M d, Y') }}
                            </p>
                            <div class="flex gap-2">
                                <a href="{{ route('products.show', $product) }}" class="text-blue-600 hover:underline">
                                    View
                                </a>
                                @can('edit products')
                                @if(auth()->user()->hasRole('Admin') || $product->user_id === auth()->id())
                                <a href="{{ route('products.edit', $product) }}" class="text-yellow-600 hover:underline">
                                    Edit
                                </a>
                                <!-- Delete Form -->
                                <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="inline-block"
                                    onsubmit="return confirm('Are you sure you want to delete this product?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline bg-transparent border-none p-0">
                                        Delete
                                    </button>
                                </form>
                                @endif
                                @endcan
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-6">
                        {{ $products->withQueryString()->links() }}
                    </div>
                    @else
                    <p class="text-gray-500 text-center py-8">No products found.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>