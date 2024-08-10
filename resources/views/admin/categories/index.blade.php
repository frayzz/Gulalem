@extends('layouts.admin')

@section('content')
    <h1 class="text-xl font-bold mb-4">Categories</h1>
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
    </div>
    <a href="{{ route('admin.categories.create') }}" class="bg-blue-500 text-white py-2 px-4 rounded mb-4 inline-block">Add Category</a>
    <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden">
        <thead class="bg-gray-200">
        <tr>
            <th class="py-2 px-4">Name</th>
            <th class="py-2 px-4">Slug</th>
            <th class="py-2 px-4">Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach($categories as $category)
            <tr class="border-t">
                <td class="py-2 px-4">{{ $category->name }}</td>
                <td class="py-2 px-4">{{ $category->slug }}</td>
                <td class="py-2 px-4 flex space-x-2">
                    <a href="{{ route('admin.categories.edit', $category) }}" class="bg-yellow-500 text-white py-1 px-2 rounded">Edit</a>
                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST">
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
