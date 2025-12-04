@extends('layouts.app')

@section('title', '🌷Купить ' . $product->name)
@section('description', $product->description)

@section('og_title', '🌷Купить ' . $product->name)
@section('og_description', $product->description )
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
        "url": "{{ url()->current() }}",
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
            @if(isset($store))
                <div class="product-store-block">
                    <a href="{{ route('store.show', ['slug' => $store->slug]) }}"
                       class="product-store-link">

                        <div class="product-store-avatar">
                            @if($store->logo)
                                <img src="{{ asset('storage/' . $store->logo) }}"
                                     alt="{{ $store->name }}">
                            @else
                                <img src="{{ asset('images/no-image.webp') }}"
                                     alt="{{ $store->name }}">
                            @endif
                        </div>

                        <div class="product-store-info">
                            <div class="product-store-title">
                                Магазин
                            </div>
                            <div class="product-store-name">
                                {{ $store->name }}
                            </div>
                        </div>

                        <div class="product-store-arrow">
                            →
                        </div>

                    </a>
                </div>
            @endif
            <div class="detail-short-description">
                <div class="product-name">
                    <h1>{{ $product->name }}</h1>
                </div>
                <div class="available-tag">
                    <!--
                    <span>новинка</span>-->
                </div>

                <div class="price">{{ $product->price }}</div>
                <div class="product-description">
                    <p>{{ $product->description . '. Доставка по городу Алматы.' }}</p>
                </div>
                @if(isset($store) && $store->isWhatsAppOrder() && $store->phone)
                    @php
                        $waPhone = preg_replace('/\D+/', '', $store->phone);
                        $waText = rawurlencode(
                            "Здравствуйте! Хочу заказать\n" . route('public.show', ['slug' => $product->slug])
                        );
                    @endphp

                    <a href="https://wa.me/{{ $waPhone }}?text={{ $waText }}"
                       target="_blank"
                       rel="noopener">
                        <button class="detail-order-button">
                            Написать в WhatsApp
                        </button>
                    </a>

                @else
                    <a href="{{ route('order.index', ['slug' => $product->slug]) }}">
                        <button class="detail-order-button">
                            Заказать
                        </button>
                    </a>
                @endif


            </div>
        </div>
    </section>
@endsection
