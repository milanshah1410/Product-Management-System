<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Product Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 space-y-6">

                    {{-- Title --}}
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">
                            {{ $product->title }}
                        </h3>
                    </div>

                    {{-- Description --}}
                    <div>
                        <h4 class="text-sm font-medium text-gray-600 mb-1">
                            Description
                        </h4>
                        <div class="prose max-w-none text-gray-800">
                            {!! nl2br(e($product->description)) !!}
                        </div>
                    </div>

                    {{-- Price --}}
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium text-gray-600">Price:</span>
                        <span class="text-gray-900 font-semibold">
                            ₹ {{ number_format($product->price, 2) }}
                        </span>
                    </div>

                    {{-- Available Date --}}
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium text-gray-600">Available From:</span>
                        <span class="text-gray-900">
                            {{ $product->date_available->format('d M Y') }}
                        </span>
                    </div>

                    {{-- Status (Optional) --}}
                    @isset($product->is_active)
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium text-gray-600">Status:</span>
                        <span class="px-2 py-1 text-xs font-semibold rounded 
                                {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $product->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    @endisset

                    {{-- Action Buttons --}}
                    <div class="flex gap-3 pt-4">

                        @can('edit products')
                        @if(auth()->user()->hasRole('Admin') || $product->user_id === auth()->id())
                        {{-- Edit Button --}}
                        <a href="{{ route('products.edit', $product) }}"
                            class="px-4 py-2 bg-blue-500 hover:bg-blue-500 text-white font-semibold rounded-lg shadow transition-all duration-200">
                            Edit
                        </a>

                        {{-- Delete Button --}}
                        <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="inline-block"
                            onsubmit="return confirm('Are you sure you want to delete this product?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="px-4 py-2 bg-red-600 hover:bg-red-600 text-white font-semibold rounded-lg shadow transition-all duration-200">
                                Delete
                            </button>
                        </form>
                        @endif
                        @endcan

                        {{-- Back Button --}}
                        <a href="{{ route('products.index') }}"
                            class="px-4 py-2 bg-gray-500 hover:bg-gray-700 text-white font-semibold rounded-lg shadow transition-all duration-200">
                            Back
                        </a>

                    </div>


                </div>
            </div>
        </div>
    </div>
</x-app-layout>