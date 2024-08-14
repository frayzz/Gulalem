@extends('layouts.app')

@section('title', $title)
@section('description', $metaDescription)

@section('structured_data')
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "ItemList",
            "name": "{{ $category->name }}",
            "description": "{{ $category->description }}",
            "itemListElement": [
            @foreach($products as $index => $product)
            {
              "@type": "Product",
              "position": {{ $index + 1 }},
              "name": "{{ $product->name }}",
              "image": "{{ secure_asset('storage/' . $product->images->first()->image_url) }}",
              "description": "{{ $product->description }}",
              "sku": "{{ $product->sku }}",
              "offers": {
                "@type": "Offer",
                "url": "{{ route('public.show', ['slug' => $product->slug]) }}",
                "priceCurrency": "KZT",
                "price": "{{ $product->price }}",
                "availability": "https://schema.org/InStock",
                "itemCondition": "https://schema.org/NewCondition"
              }
            }@if(!$loop->last),@endif
        @endforeach
        ]
      }
    </script>
@endsection

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
                                        <img src="{{ secure_asset('storage/' . $product->images->first()->image_url) }}" loading="lazy" alt="{{ $product->name }}" class="w-24 h-24 object-cover">
                                    </picture>
                                @else
                                    <img src="{{ secure_asset('images/no-image.png') }}" loading="lazy" alt="No image" class="w-24 h-24 object-cover">
                                @endif
                            </a>
                        </div>
                        <div class="short-description">
                            <a href="{{ route('public.show', ['slug' => $product->slug]) }}">
                                <div class="product-name">
                                    <h4>{{ $product->name }}</h4>
                                </div>
                            </a>
                            <meta content="KZT">
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
