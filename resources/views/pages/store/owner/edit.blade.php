@extends('layouts.app')

@section('title', 'Мой магазин')

@section('content')
    <section class="filters">
        <h1>Мой магазин</h1>

        <p style="margin-top:8px; color:#555;">
            Здесь вы можете изменить название, ссылку, контактные данные и логотип магазина.
        </p>

        <div style="margin-top: 12px;">
            <a href="{{ route('store.products.index') }}" style="font-size:14px; color:#007BA1; text-decoration:none;">
                ← Вернуться к товарам
            </a>
        </div>
    </section>

    <section class="order">
        @if(session('success'))
            <div style="margin-bottom:15px; padding:10px 12px; border-radius:10px; background:#dcfce7; color:#166534; font-size:14px;">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('store.update') }}"
              method="POST"
              enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Основная информация --}}
            <div class="input-container">
                <h3>Основная информация</h3>

                <label style="display:block; margin-top:10px;">Название магазина</label>
                <input type="text"
                       name="name"
                       class="custom-input"
                       value="{{ old('name', $store->name) }}"
                       required>
                @error('name')<div style="color:red;font-size:12px;">{{ $message }}</div>@enderror

                <label style="display:block; margin-top:10px;">Ссылка (slug)</label>
                <input type="text"
                       name="slug"
                       class="custom-input"
                       value="{{ old('slug', $store->slug) }}"
                       required>
                <small style="font-size:12px; color:#666;">
                    Публичная страница: https://gulalem.kz/store/<b>{{ old('slug', $store->slug) }}</b>
                </small>
                @error('slug')<div style="color:red;font-size:12px;">{{ $message }}</div>@enderror

                <label style="display:block; margin-top:10px;">Описание магазина</label>
                <textarea name="description"
                          class="custom-input"
                          rows="4"
                          style="width:100%;">{{ old('description', $store->description) }}</textarea>
            </div>

            {{-- Адрес и контакты --}}
            <div class="input-container">
                <h3>Адрес и контакты</h3>

                <label style="display:block; margin-top:10px;">Город</label>
                <input type="text"
                       name="city"
                       class="custom-input"
                       value="{{ old('city', $store->city) }}">

                <label style="display:block; margin-top:10px;">Регион</label>
                <input type="text"
                       name="region"
                       class="custom-input"
                       value="{{ old('region', $store->region) }}">

                <label style="display:block; margin-top:10px;">Улица</label>
                <input type="text"
                       name="street"
                       class="custom-input"
                       value="{{ old('street', $store->street) }}">

                <label style="display:block; margin-top:10px;">Почтовый индекс</label>
                <input type="text"
                       name="postal_code"
                       class="custom-input"
                       value="{{ old('postal_code', $store->postal_code) }}">

                <label style="display:block; margin-top:10px;">Телефон</label>
                <input type="text"
                       name="phone"
                       class="custom-input"
                       value="{{ old('phone', $store->phone) }}">
            </div>

            {{-- Логотип --}}
            <div class="input-container">
                <h3>Логотип</h3>

                @if($store->logo)
                    <div style="margin:10px 0;">
                        <img src="{{ asset('storage/' . $store->logo) }}"
                             alt="Логотип магазина"
                             style="width:80px;height:80px;border-radius:50%;object-fit:cover;">
                    </div>
                @endif

                <label style="display:block; margin-top:10px;">Загрузить новый логотип</label>
                <input type="file" name="logo">

                @error('logo')<div style="color:red;font-size:12px;">{{ $message }}</div>@enderror
            </div>

            <button class="order-button" style="margin-top:15px; width:100%;">
                Сохранить изменения
            </button>
        </form>
    </section>
@endsection
