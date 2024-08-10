@extends('layouts.app')

@section('title', 'Категории цветов Gulalem. Заказать с доставкой по Алмате')
@section('description', 'Просмотр всех категорий цветов для любого повода, что бы обрадовать своих любимых')

@section('content')
    <section class="categories-section">
        <h1 class="text-2xl font-bold mb-6">Категории</h1>
        <ul class="categories-list">
            @foreach($categories->where('parent_id', null) as $parent)
                <li class="category-item">
                    <a href="{{ route('categories.show', $parent->slug) }}" class="category-name">{{ $parent->name }}</a>
                    @if($parent->children->isNotEmpty())
                        <ul class="subcategories-list">
                            @foreach($parent->children as $child)
                                <li class="subcategory-item">
                                    <a href="{{ route('categories.show', $child->slug) }}" class="subcategory-name">{{ $child->name }}</a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endforeach
        </ul>
    </section>
@endsection
