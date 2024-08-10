@extends('layouts.app')

@section('title', $title)
@section('description', $metaDescription)

@section('content')
    <section class="category-detail">
        <div class="category-header">
            <h1 class="text-2xl font-bold mb-2">{{ $category->name }}</h1>
            @if($category->description)
                <div class="category-description mb-4">
                    <p>{{ $category->description }}</p>
                </div>
            @endif
        </div>


        @if($products->isEmpty())
            <p>В этой категории пока нет товаров.</p>
        @else
            <div class="products-list">
                @foreach($products as $product)
                    <div class="product">
                        <div class="illustration">
                            <a href="{{ route('public.show', ['slug' => $product->slug]) }}">
                                @if($product->images->isNotEmpty())
                                    <img src="{{ asset('storage/' . $product->images->first()->image_url) }}" alt="{{ $product->name }}" class="w-24 h-24 object-cover">
                                @else
                                    <img src="{{ asset('images/no-image.png') }}" alt="{{ $product->name }}" class="w-24 h-24 object-cover">
                                @endif
                            </a>
                        </div>
                        <div class="short-description">
                            <a href="{{ route('public.show', ['slug' => $product->slug]) }}">
                                <h4 class="product-name">{{ $product->name }}</h4>
                            </a>
                            <div class="price">{{ $product->price }}₸</div>
                        </div>
                        <div class="nav-buttons">
                            <a href="{{ route('order.index', ['slug' => $product->slug]) }}">
                                <button class="order-button">Заказать</button>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>
@endsection
