@extends('layouts.app')

@section('title', $meta['title'])
@section('description', $meta['description'])

@section('structured_data')
    {{-- Магазин как Store --}}
    <script type="application/ld+json">
        {
          "@context": "https://schema.org",
          "@type": "Store",
          "name": "{{ $shop->name }}",
          "description": "{{ $meta['description'] }}",
          "image": "{{ $meta['og_image'] }}",
          "url": "{{ route('shop.show', ['slug' => $shop->slug]) }}",
          "address": {
            "@type": "PostalAddress",
            "addressLocality": "{{ $shop->city ?? 'Алматы' }}",
            "addressRegion": "{{ $shop->region ?? 'KZ' }}",
            "streetAddress": "{{ $shop->street ?? 'Толе би' }}",
            "postalCode": "{{ $shop->postal_code ?? '050000' }}",
            "addressCountry": "KZ"
          },
          "telephone": "{{ $shop->phone ?? '' }}"
        }
    </script>

    {{-- Список товаров магазина --}}
    @if($products->isNotEmpty())
        <script type="application/ld+json">
            {
              "@context": "https://schema.org",
              "@type": "ItemList",
              "itemListElement": [
                @foreach($products as $index => $product)
                    {
                      "@type": "Product",
                      "position": {{ $index + 1 }},
                      "name": "{{ $product->name }}",
                      @if($product->images->isNotEmpty())
                      "image": "{{ secure_asset('storage/' . $product->images->first()->image_url) }}",
                      @endif
                      "description": "{{ \Illuminate\Support\Str::limit(trim(preg_replace('/\s+/', ' ', $product->description)), 200, '...') }}",
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
    @endif
@endsection

@section('content')
    <section class="shop-header">
        <div class="shop-info">
            <div class="shop-logo">
                @if($shop->logo)
                    <img src="{{ asset('storage/' . $shop->logo) }}"
                         alt="{{ $shop->name }}"
                         class="w-24 h-24 object-cover rounded-full">
                @else
                    <img src="{{ asset('images/no-image.png') }}"
                         alt="{{ $shop->name }}"
                         class="w-24 h-24 object-cover rounded-full">
                @endif
            </div>

            <div class="shop-text">
                <h1>{{ $shop->name }}</h1>

                @if($shop->description)
                    <p>{{ $shop->description }}</p>
                @endif

                <div class="shop-meta">
                    @if($shop->city || $shop->street)
                        <div class="shop-address">
                            📍 {{ $shop->city ?? 'Алматы' }}@if($shop->street), {{ $shop->street }}@endif
                        </div>
                    @endif

                    @if($shop->phone)
                        <div class="shop-phone">
                            📞 <a href="tel:{{ $shop->phone }}">{{ $shop->phone }}</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <section class="shop-products">
        <h2>Товары магазина</h2>

        @if($products->isEmpty())
            <p>У этого магазина пока нет активных товаров.</p>
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
                                                <source srcset="{{ asset('storage/' . $image->image_url) }}"
                                                        type="image/{{ $image->type }}"
                                                        media="(max-width: 600px)">
                                            @elseif($image->format == 'medium')
                                                <source srcset="{{ asset('storage/' . $image->image_url) }}"
                                                        type="image/{{ $image->type }}"
                                                        media="(max-width: 1200px)">
                                            @elseif($image->format == 'large')
                                                <source srcset="{{ asset('storage/' . $image->image_url) }}"
                                                        type="image/{{ $image->type }}">
                                            @endif
                                        @endforeach

                                        <img src="{{ secure_asset('storage/' . $product->images->first()->image_url) }}"
                                             loading="lazy"
                                             alt="{{ $product->name }}"
                                             class="w-24 h-24 object-cover">
                                    </picture>
                                @else
                                    <img src="{{ secure_asset('images/no-image.png') }}"
                                         loading="lazy"
                                         alt="No image"
                                         class="w-24 h-24 object-cover">
                                @endif
                            </a>
                        </div>

                        <div class="short-description">
                            <a href="{{ route('public.show', ['slug' => $product->slug]) }}">
                                <div class="product-name">
                                    <h4>{{ $product->name }}</h4>
                                </div>
                            </a>
                            <div class="price">{{ number_format($product->price, 0, '.', ' ') }}₸</div>
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
