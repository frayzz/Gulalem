@extends('layouts.app')

@section('title', 'Gulalem - твой цветочный мир')

@section('content')
    <section>
        <div class="active-orders">
            @if( isset($activeOrders) )
                @php
                    echo count($activeOrders);
                @endphp
            @endif
        </div>
        <div class="filters">
            <form method="GET" action="{{ route('public.index') }}">
                    <select class="custom-select" name="sort" onchange="this.form.submit()">
                        <option value="new" {{ request('sort') == 'new' ? 'selected' : '' }}>Новинки</option>
                        <option value="price_low_high" {{ request('sort') == 'price_low_high' ? 'selected' : '' }}>Цена: по возрастанию</option>
                        <option value="price_high_low" {{ request('sort') == 'price_high_low' ? 'selected' : '' }}>Цена: по убыванию</option>
                    </select>
                    <select class="custom-select" name="price" onchange="this.form.submit()">
                        <option value="">Любая цена</option>
                        <option value="15000" {{ request('price') == '15000' ? 'selected' : '' }}>До 15 000тг</option>
                        <option value="20000" {{ request('price') == '20000' ? 'selected' : '' }}>До 20 000тг</option>
                        <option value="40000" {{ request('price') == '40000' ? 'selected' : '' }}>До 40 000тг</option>
                        <option value="60000" {{ request('price') == '60000' ? 'selected' : '' }}>До 60 000тг</option>
                    </select>
            </form>
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
                        <a href="{{ route('order.index', $product->id) }}">
                            <button class="order-button">Заказать</button>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endsection
