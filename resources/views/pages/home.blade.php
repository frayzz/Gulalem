@extends('layouts.app')

@section('title', 'Gulalem - твой цветочный мир')

@section('content')
    <section>
        @if( isset($activeOrders) )
            @php
                //dd($activeOrders->first());
                $order = $activeOrders->last();
            @endphp
            @if ($order->status != 'end')
                <div class="active-orders">
                    <div>
                        <p>Номер заказа.{{ $order->id }}</p>
                        <h2>{{ __('status.'.$order->status) }}</h2>
                    </div>
                    <div class="loading-container">
                        <div class="stage {{ $order->status == 'not-pay' ? 'loading' : 'completed' }}"></div>
                        <div class="stage {{ $order->status == 'awaiting-packing' ? 'loading' : ($order->status == 'not-pay' ? 'not-started' : 'completed') }}"></div>
                        <div class="stage {{ $order->status == 'packing' ? 'loading' : ($order->status == 'delivery' ? 'completed' : 'not-started') }}"></div>
                        <div class="stage {{ $order->status == 'delivery' ? 'loading' : 'not-started' }}"></div>
                    </div>
                </div>
            @endif
        @endif
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
                                        <img src="{{ asset('storage/' . $product->images->first()->image_url) }}" loading="lazy" alt="{{ $product->name }}" itemprop="image" class="w-24 h-24 object-cover">
                                    </picture>
                                </div>
                            @else
                                <div itemscope itemtype="http://schema.org/Product">
                                    <img src="{{ asset('images/no-image.png') }}" loading="lazy" alt="No image" itemprop="image" class="w-24 h-24 object-cover">
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
    </section>
@endsection
