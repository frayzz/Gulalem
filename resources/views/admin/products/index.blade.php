@extends('layouts.admin')

@section('content')
    <h1 class="text-xl font-bold mb-4">Products</h1>
    <a href="{{ route('admin.products.create') }}" class="bg-blue-500 text-white py-2 px-4 rounded mb-4 inline-block">Add Product</a>
    <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden">
        <thead class="bg-gray-200">
        <tr>
            <th class="py-2 px-4">Name</th>
            <th class="py-2 px-4">Price</th>
            <th class="py-2 px-4">Store</th>
            <th class="py-2 px-4">Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach($products as $product)
            <tr class="border-t">
                <td class="py-2 px-4">{{ $product->name }}</td>
                <td class="py-2 px-4">{{ $product->price }}</td>
                <td class="py-2 px-4">{{ $product->store->name }}</td>
                <td class="py-2 px-4 flex space-x-2">
                    <a href="{{ route('admin.products.edit', $product) }}" class="bg-yellow-500 text-white py-1 px-2 rounded">Edit</a>
                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 text-white py-1 px-2 rounded">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
