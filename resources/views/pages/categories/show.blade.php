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
                                    <div itemscope itemtype="http://schema.org/Product">
                                        <picture>
                                            @foreach($product->images as $image)
                                                @if($image->format == 'thumbnail')
                                                    <source srcset="{{ asset('storage/' . $image->image_url) }}" type="image/{{ $image->type }}" media="(max-width: 600px)">
                                                @elseif($image->format == 'medium')
                                                    <source srcset="{{ asset('storage/' . $image->image_url) }}" type="image/{{ $image->type }}" media="(max-width: 1200px)">
                                                @elseif($image->format == 'large')
                                                    <source srcset="{{ asset('storage/' . $image->image_url) }}" type="image/{{ $image->type }}">
                                                @endif
                                            @endforeach
                                            <img src="{{ secure_asset('storage/' . $product->images->first()->image_url) }}" loading="lazy" alt="{{ $product->name }}" itemprop="image" class="w-24 h-24 object-cover">
                                        </picture>
                                    </div>
                                @else
                                    <div itemscope itemtype="http://schema.org/Product">
                                        <img src="{{ secure_asset('images/no-image.png') }}" loading="lazy" alt="No image" itemprop="image" class="w-24 h-24 object-cover">
                                    </div>
                                @endif
                            </a>
                        </div>
                        <div class="short-description">
                            <a href="{{ route('public.show', ['slug' => $product->slug]) }}">
                                <div class="product-name">
                                    <h4 itemprop="name">{{ $product->name }}</h4>
                                </div>
                            </a>

                            <meta itemprop="priceCurrency" content="KZT">
                            <div class="price" itemprop="price">{{ $product->price }}₸</div>
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
