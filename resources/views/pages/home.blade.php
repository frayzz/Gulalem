@extends('layouts.app')

@section('title', 'Gulalem - твой цветочный мир')
@section('description', 'Gulalem - лучший интернет-магазин цветов. Заказывайте свежие букеты и цветочные композиции с доставкой по Алмате.')

@section('structured_data')
    <script type="application/ld+json">
        {
          "@context": "https://schema.org",
          "@type": "Store",
          "name": "Gulalem",
          "description": "Лучший интернет-магазин цветов. Заказывайте свежие букеты и цветочные композиции с доставкой по Алмате.",
          "image": "{{ secure_asset('images/favicon.ico') }}",
          "url": "{{ url('/') }}",
          "address": {
            "@type": "PostalAddress",
            "addressLocality": "Алматы",
            "addressRegion": "KZ",
            "streetAddress": "Толе би",
            "postalCode": "050000",
            "addressCountry": "KZ"
          }
}
    </script>

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
              "image": "{{ secure_asset('storage/' . $product->images->first()->image_url) }}",
              "description": "{{ $product->description }}",
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
    <section>
        <div class="products-list">
            @foreach($products as $product)
                <div class="product" itemscope itemtype="http://schema.org/Product">
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
                                    <img src="{{ secure_asset('storage/' . $product->images->first()->image_url) }}" loading="lazy" alt="{{ $product->name }}" itemprop="image" class="w-24 h-24 object-cover">
                                </picture>
                            @else
                                <img src="{{ secure_asset('images/no-image.png') }}" loading="lazy" alt="No image" itemprop="image" class="w-24 h-24 object-cover">
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
    </section>
@endsection
