@extends('layouts.admin')

@section('content')
    <h1 class="text-xl font-bold mb-4">Add Product</h1>
    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
            <input type="text" name="name" id="name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
        </div>
        <div>
            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
            <textarea name="description" id="description" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
        </div>
        <div>
            <label for="price" class="block text-sm font-medium text-gray-700">Price</label>
            <input type="number" name="price" id="price" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
        </div>
        <div>
            <label for="store_id" class="block text-sm font-medium text-gray-700">Store</label>
            <select name="store_id" id="store_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                @foreach($stores as $store)
                    <option value="{{ $store->id }}">{{ $store->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="category_id" class="block text-sm font-medium text-gray-700">Category</label>
            <select name="category_id" id="category_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                <option value="" selected disabled hidden>Select Category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id ?? '') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="images" class="block text-sm font-medium text-gray-700">Images</label>
            <input type="file" name="images[]" id="images" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" multiple>
        </div>
        <div>
            <label for="properties" class="block text-sm font-medium text-gray-700">Properties</label>
            <div id="properties-container" class="space-y-2">
                <div class="flex space-x-2">
                    <input type="text" name="properties[0][name]" placeholder="Property Name" class="block w-full border-gray-300 rounded-md shadow-sm">
                    <input type="text" name="properties[0][value]" placeholder="Property Value" class="block w-full border-gray-300 rounded-md shadow-sm">
                </div>
            </div>
            <button type="button" id="add-property" class="mt-2 bg-gray-500 text-white py-1 px-2 rounded">Add Property</button>
        </div>
        <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded">Add Product</button>
    </form>

    <script>
        document.getElementById('add-property').addEventListener('click', function () {
            const container = document.getElementById('properties-container');
            const index = container.children.length;
            const newProperty = document.createElement('div');
            newProperty.classList.add('flex', 'space-x-2', 'mt-2');
            newProperty.innerHTML = `
                <input type="text" name="properties[${index}][name]" placeholder="Property Name" class="block w-full border-gray-300 rounded-md shadow-sm">
                <input type="text" name="properties[${index}][value]" placeholder="Property Value" class="block w-full border-gray-300 rounded-md shadow-sm">
            `;
            container.appendChild(newProperty);
        });
    </script>
@endsection
