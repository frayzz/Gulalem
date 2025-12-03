@extends('layouts.app')

@section('title', 'Мои товары')

@section('content')
    <section class="filters">
        <h1>Товары магазина: {{ $store->name }}</h1>

        <div style="margin-top: 15px; display:flex; gap:10px; flex-wrap:wrap;">
            <a href="{{ route('store.edit') }}">
                <button class="order-button" style="font-size:16px;padding:10px 16px;">
                    ⚙️ Редактировать магазин
                </button>
            </a>

            <a href="{{ route('store.products.create') }}">
                <button class="order-button">
                    + Добавить товар
                </button>
            </a>
        </div>
    </section>

    <section class="shop-products">
        @if($products->isEmpty())
            <p>У вас пока нет товаров. Добавьте первый.</p>
        @else
            <div class="products-list">
                @foreach($products as $product)
                    <div class="product">
                        <div class="illustration">
                            <a href="{{ route('public.show', ['slug' => $product->slug ?? '#']) }}">
                                @if($product->images->isNotEmpty())
                                    <img src="{{ asset('storage/' . $product->images->first()->image_url) }}"
                                         alt="{{ $product->name }}">
                                @else
                                    <img src="{{ asset('images/no-image.webp') }}"
                                         alt="{{ $product->name }}">
                                @endif
                            </a>
                        </div>

                        <div class="short-description">
                            <div class="product-name">
                                <h4>{{ $product->name }}</h4>
                                <p style="font-size: 12px; color: #777;">
                                    В наличии: {{ $product->inventory->quantity ?? 0 }}
                                </p>
                            </div>
                            <div class="price">
                                {{ number_format($product->price, 0, '.', ' ') }} ₸
                            </div>
                        </div>

                        <div class="nav-buttons" style="display: flex; justify-content: space-between; gap: 8px;">
                            <a href="{{ route('store.products.edit', $product) }}">
                                <button class="order-button" style="font-size: 16px;">Редактировать</button>
                            </a>

                            <form action="{{ route('store.products.destroy', $product) }}" method="POST"
                                  onsubmit="return confirm('Удалить товар?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="order-button"
                                        style="background-color:#e11d48; font-size:16px;">
                                    Удалить
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div style="margin-top: 20px;">
                {{ $products->links() }}
            </div>
        @endif
    </section>
@endsection
