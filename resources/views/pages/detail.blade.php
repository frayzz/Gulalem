@extends('layouts.app')

@section('title', $meta['title'])
@section('description', $meta['description'])

@section('og_title', $meta['og_title'])
@section('og_description', $meta['og_description'])
@section('og_image', $meta['og_image'])

@section('content')

    <section>
        <div class="product-detail">
            <div class="detail-illustration">
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
                        <img src="{{ asset('storage/' . $product->images->first()->image_url) }}" alt="{{ $product->name }}" class="w-24 h-24 object-cover">
                    </picture>
                @else
                    <img src="{{ asset('images/no-image.png') }}" alt="No image" class="w-24 h-24 object-cover">
                @endif
            </div>
            <div class="detail-short-description">
                <div class="product-name">
                    <h1>{{ $product->name }}</h1>
                </div>
                <div class="available-tag">
                    <span>новинка</span>
                </div>
                <div class="price">{{ $product->price }}</div>
                <div class="product-description">
                    <p>{{ $product->description }}</p>
                </div>
                <a href="{{ route('order.index') }}">
                    <button class="detail-order-button">Заказать</button>
                </a>

            </div>
        </div>
    </section>
@endsection
