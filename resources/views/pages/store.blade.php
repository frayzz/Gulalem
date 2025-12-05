@extends('layouts.app')

@php
    $ogImage = $store->logo
        ? asset('storage/' . $store->logo)
        : asset('images/no-image.webp');
@endphp

@section('title', 'Каталог магазина - ' . $store->name)
@section('description', $store->description)

@section('title', 'Каталог магазина - ' . $store->name)
@section('description', $store->description)

@section('og_title', 'Каталог магазина - ' . $store->name)
@section('og_description', $store->description )
@section('og_image', $ogImage)

@section('structured_data')
    {{-- Магазин как Store --}}
    <script type="application/ld+json">
        {
          "@context": "https://schema.org",
          "@type": "Store",
          "name": "{{ $store->name }}",
          "description": "{{ $meta['description'] }}",
          "image": "{{ $meta['og_image'] }}",
          "url": "{{ "https://gulalem.kz/store/" . $store->slug }}",
          "address": {
            "@type": "PostalAddress",
            "addressLocality": "{{ $store->city ?? 'Алматы' }}",
            "addressRegion": "{{ $store->region ?? 'KZ' }}",
            "streetAddress": "{{ $store->street ?? 'Толе би' }}",
            "postalCode": "{{ $store->postal_code ?? '050000' }}",
            "addressCountry": "KZ"
          },
          "telephone": "{{ $store->phone ?? '' }}"
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
                @if($store->logo)
                    <img src="{{ asset('storage/' . $store->logo) }}"
                         alt="{{ $store->name }}"
                         class="w-24 h-24 object-cover rounded-full">
                @else
                    <img src="{{ asset('images/no-image.webp') }}"
                         alt="{{ $store->name }}"
                         class="w-24 h-24 object-cover rounded-full">
                @endif
            </div>

            <div class="shop-text">
                <h1>{{ $store->name }}</h1>

                @if($store->description)
                    <p>{{ $store->description }}</p>
                @endif

                <div class="shop-meta">
                    @if($store->city || $store->street)
                        <div class="shop-address">
                            📍 {{ $store->city ?? 'Алматы' }}@if($store->street), {{ $store->street }}@endif
                        </div>
                    @endif

                    @if($store->phone)
                        <div class="shop-phone">
                            📞 <a href="tel:{{ $store->phone }}">{{ $store->phone }}</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <section class="shop-products">

        {{-- Фильтры по цене и сортировке --}}
        <form method="GET"
              action="{{ route('store.show', ['slug' => $store->slug]) }}"
              class="shop-filters">
            {{-- Фильтр по цене --}}
            <div>
                <label for="price" style="font-size:14px; color:#555;"></label>
                <select name="price"
                        id="price"
                        onchange="this.form.submit()"
                        style="padding:6px 8px; border-radius:8px; border:1px solid #ddd; font-size:14px;">
                    <option value="" {{ empty($price) ? 'selected' : '' }}>Цена</option>
                    <option value="15000" {{ $price === '15000' ? 'selected' : '' }}>до 15 000 ₸</option>
                    <option value="20000" {{ $price === '20000' ? 'selected' : '' }}>до 20 000 ₸</option>
                    <option value="40000" {{ $price === '40000' ? 'selected' : '' }}>до 40 000 ₸</option>
                    <option value="60000" {{ $price === '60000' ? 'selected' : '' }}>до 60 000 ₸</option>
                </select>
            </div>

            {{-- Сортировка по цене --}}
            <div>
                <label for="sort" style="font-size:14px; color:#555;"></label>
                <select name="sort"
                        id="sort"
                        onchange="this.form.submit()
"
                        style="padding:6px 8px; border-radius:8px; border:1px solid #ddd; font-size:14px;">
                    <option value="" {{ empty($sort) ? 'selected' : '' }}>Сортировка</option>
                    <option value="price_asc" {{ $sort === 'price_asc' ? 'selected' : '' }}>По цене (дешевле → дороже)</option>
                    <option value="price_desc" {{ $sort === 'price_desc' ? 'selected' : '' }}>По цене (дороже → дешевле)</option>
                </select>
            </div>
        </form>

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
                            @if($store->isWhatsAppOrder() && $store->phone)
                                @php
                                    $waPhone = preg_replace('/\D+/', '', $store->phone);
                                    $waText = rawurlencode(
                                        "Здравствуйте! Хочу заказать\n" . route('public.show', ['slug' => $product->slug])
                                    );
                                @endphp

                                <a href="https://wa.me/{{ $waPhone }}?text={{ $waText }}"
                                   target="_blank"
                                   rel="noopener">
                                    <button class="order-button">
                                        WhatsApp
                                    </button>
                                </a>

                            @else
                                <a href="{{ route('order.index', ['slug' => $product->slug]) }}">
                                    <button class="order-button">
                                        Заказать
                                    </button>
                                </a>
                            @endif
                        </div>

                    </div>
                @endforeach
            </div>
        @endif
    </section>
@endsection
