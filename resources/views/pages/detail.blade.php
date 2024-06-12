@extends('layouts.app')

@section('title', 'Gulalem - твой цветочный мир')

@section('content')

    <section>
        <div class="product-detail">
            <div class="detail-illustration">
                <img src="{{ asset('images/photo_2024-05-26_14-43-06.jpg') }}" alt="">
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
