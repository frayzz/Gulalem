@extends('layouts.app')

@section('title', 'Мои товары')

@section('content')
    <section class="store-dashboard-filters">
        <div class="store-dashboard-header-row">
            <div class="store-dashboard-header-left">
                <a href="{{ route('store.show', ['slug' => $store->slug]) }}"
                   class="store-dashboard-back-link"
                   target="_blank">
                    ← Вернуться в магазин
                </a>
                <h1 class="store-dashboard-title">
                    Товары магазина: {{ $store->name }}
                </h1>
            </div>
        </div>


        {{-- Сообщения об успехе --}}
        @if(session('success'))
            <div class="store-dashboard-alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="store-dashboard-actions-row">
            <a href="{{ route('store.edit') }}">
                <button class="order-button store-dashboard-btn-main">
                    ⚙️ Редактировать магазин
                </button>
            </a>

            <a href="{{ route('store.products.create') }}">
                <button class="order-button store-dashboard-btn-secondary">
                    + Добавить товар
                </button>
            </a>

            {{-- Фильтр по статусу --}}
            <form method="GET"
                  action="{{ route('store.products.index') }}"
                  class="store-dashboard-status-filter">
                <label for="status" class="store-dashboard-status-label">Статус:</label>
                <select name="status"
                        id="status"
                        onchange="this.form.submit()"
                        class="store-dashboard-status-select">
                    <option value="" {{ $status === null ? 'selected' : '' }}>Все</option>
                    <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Только активные</option>
                    <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Только скрытые</option>
                </select>
            </form>
        </div>
    </section>

    <section class="store-dashboard-products">
        @if($products->isEmpty())
            <p class="store-dashboard-empty">У вас пока нет товаров. Добавьте первый.</p>
        @else
            <div class="store-dashboard-products-list">
                @foreach($products as $product)
                    <div class="store-dashboard-product-card">
                        <div class="store-dashboard-product-image-wrap">
                            <a href="{{ route('public.show', ['slug' => $product->slug ?? '#']) }}" target="_blank">
                                @if($product->images->isNotEmpty())
                                    <img src="{{ asset('storage/' . $product->images->first()->image_url) }}"
                                         alt="{{ $product->name }}"
                                         class="store-dashboard-product-image">
                                @else
                                    <img src="{{ asset('images/no-image.webp') }}"
                                         alt="{{ $product->name }}"
                                         class="store-dashboard-product-image">
                                @endif
                            </a>
                        </div>

                        <div class="store-dashboard-product-main">
                            <div class="store-dashboard-product-info">
                                <div class="store-dashboard-product-name-block">
                                    <h4 class="store-dashboard-product-name">
                                        {{ $product->name }}
                                    </h4>
                                    <p class="store-dashboard-product-qty">
                                        В наличии: {{ $product->inventory->quantity ?? 0 }}
                                    </p>

                                    <div class="store-dashboard-product-status">
                                        @if($product->is_active)
                                            <span class="store-dashboard-badge store-dashboard-badge-active">
                                                Активен
                                            </span>
                                        @else
                                            <span class="store-dashboard-badge store-dashboard-badge-inactive">
                                                Скрыт
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="store-dashboard-product-price">
                                    {{ number_format($product->price, 0, '.', ' ') }} ₸
                                </div>
                            </div>

                            <div class="store-dashboard-product-actions">
                                {{-- Переключатель активности --}}
                                <form action="{{ route('store.products.toggle-status', $product) }}"
                                      method="POST"
                                      class="store-dashboard-product-action-form">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="order-button store-dashboard-btn-toggle {{ $product->is_active ? '' : 'store-dashboard-btn-toggle-inactive' }}">
                                        {{ $product->is_active ? 'Скрыть с витрины' : 'Показать на витрине' }}
                                    </button>
                                </form>

                                {{-- Редактировать --}}
                                <a href="{{ route('store.products.edit', $product) }}"
                                   class="store-dashboard-product-action-link">
                                    <button class="order-button store-dashboard-btn-edit">
                                        Редактировать
                                    </button>
                                </a>

                                {{-- Удалить --}}
                                <form action="{{ route('store.products.destroy', $product) }}"
                                      method="POST"
                                      onsubmit="return confirm('Удалить товар?');"
                                      class="store-dashboard-product-action-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="order-button store-dashboard-btn-delete">
                                        Удалить
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="store-dashboard-pagination">
                {{ $products->links() }}
            </div>
        @endif
    </section>
@endsection
