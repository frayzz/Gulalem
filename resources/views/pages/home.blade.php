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
                            <img src="{{ asset('images/photo_2024-05-26_14-43-06.jpg') }}" alt="">
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
