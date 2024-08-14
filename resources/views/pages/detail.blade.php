@extends('layouts.app')

@section('title', '🌷Купить ' . $product->name . ' в Алмате с доставкой | Gulalem')
@section('description', $product->description . '. Доставка по городу Алматы. Лучшие цены на ' . $product->name . ' в нашем интернет-магазине.')

@section('og_title', '🌷Купить ' . $product->name . ' в Алматы')
@section('og_description', 'Лучшие цены на ' . $product->description . '. Доставка по городу Алматы.')
@section('og_image', secure_asset('storage/' . $product->images->first()->image_url))

@section('structured_data')
    <script type="application/ld+json">
        {
          "@context": "https://schema.org",
          "@type": "Product",
          "name": "{{ $product->name }}",
      "image": "{{ secure_asset('storage/' . $product->images->first()->image_url) }}",
      "description": "{{ $product->description }}",
      "offers": {
        "@type": "Offer",
        "url": "{{ route('order.index', ['slug' => $product->slug]) }}",
        "priceCurrency": "KZT",
        "price": "{{ $product->price }}",
        "itemCondition": "https://schema.org/NewCondition",
        "availability": "https://schema.org/InStock"
      }
    }
    </script>
@endsection

@section('content')

    <section>
        <div class="product-detail">
            <div class="detail-illustration">
                @if($product->images->isNotEmpty())
                    <picture>
                        @foreach($product->images as $image)
                            @if($image->format == 'thumbnail')
                                <source srcset="{{ secure_asset('storage/' . $image->image_url) }}" type="image/{{ $image->type }}" media="(max-width: 600px)">
                            @elseif($image->format == 'medium')
                                <source srcset="{{ secure_asset('storage/' . $image->image_url) }}" type="image/{{ $image->type }}" media="(max-width: 1200px)">
                            @elseif($image->format == 'large')
                                <source srcset="{{ secure_asset('storage/' . $image->image_url) }}" type="image/{{ $image->type }}">
                            @endif
                        @endforeach
                        <img src="{{ secure_asset('storage/' . $product->images->first()->image_url) }}" alt="{{ $product->name }}" class="w-24 h-24 object-cover">
                    </picture>
                @else
                    <img src="{{ secure_asset('images/no-image.png') }}" alt="No image" class="w-24 h-24 object-cover">
                @endif
            </div>
            <div class="detail-short-description">
                <div class="product-name">
                    <h1 itemprop="name">{{ $product->name }}</h1>
                </div>
                <div class="available-tag">
                    <span>новинка</span>
                </div>

                <meta itemprop="priceCurrency" content="KZT">
                <div class="price" itemprop="price">{{ $product->price }}</div>
                <div class="product-description" itemprop="description">
                    <p>{{ $product->description . '. Доставка по городу Алматы.' }}</p>
                </div>
                <a href="{{ route('order.index', ['slug' => $product->slug]) }}">
                    <button class="detail-order-button">Заказать</button>
                </a>

            </div>
        </div>
    </section>
@endsection
