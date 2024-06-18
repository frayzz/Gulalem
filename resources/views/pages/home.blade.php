@extends('layouts.app')

@section('title', 'Gulalem - твой цветочный мир')

@section('content')
    <section>
        <div class="filters">
            <div class="custom-select-wrapper">
                <select class="custom-select">
                    <option value="popular">Популярное</option>
                    <option value="new">Новинки</option>
                    <option value="price_low_high">Цена: по возрастанию</option>
                    <option value="price_high_low">Цена: по убыванию</option>
                </select>
            </div>
            <div class="custom-select-wrapper">
                <select class="custom-select">
                    <option value="popular">Любая цена</option>
                    <option value="new">До 15 000тг</option>
                    <option value="price_low_high">До 20 000тг</option>
                    <option value="price_high_low">До 40 000тг</option>
                    <option value="price_high_low">До 60 000тг</option>
                </select>
            </div>
        </div>

        <div class="products-list">
            @foreach($products as $product)
                <div class="product">
                    <div class="illustration">
                        <a href="{{ route('public.show', $product->id) }}">
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
                        </a>
                    </div>
                    <div class="short-description">
                        <a href="{{ route('public.show', $product->id) }}">
                            <div class="product-name">
                                <h4>{{ $product->name }}</h4>
                            </div>
                        </a>

                        <div class="price">{{ $product->price }}₸</div>
                    </div>
                    <div class="nav-buttons">
                        <a href="{{ route('order.index') }}">
                            <button class="order-button">Заказать</button>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endsection
