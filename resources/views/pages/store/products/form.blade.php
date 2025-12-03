@extends('layouts.app')

@section('title', $mode === 'create' ? 'Добавить товар' : 'Редактировать товар')

@section('content')
    <section class="order-header">
        <h1>{{ $mode === 'create' ? 'Добавить товар' : 'Редактировать товар' }}</h1>
    </section>

    <section class="order">
        <form action="{{ $mode === 'create'
                ? route('store.products.store')
                : route('store.products.update', $product) }}"
              method="POST"
              enctype="multipart/form-data">
            @csrf
            @if($mode === 'edit')
                @method('PUT')
            @endif

            <div class="input-container">
                <label for="name">Название товара</label>
                <input id="name" type="text" name="name" class="custom-input"
                       value="{{ old('name', $product->name) }}" required>
                @error('name')<div style="color:red;font-size:12px;">{{ $message }}</div>@enderror
            </div>

            <div class="input-container">
                <label for="description">Описание</label>
                <textarea id="description" name="description" rows="4" class="custom-input">{{ old('description', $product->description) }}</textarea>
                @error('description')<div style="color:red;font-size:12px;">{{ $message }}</div>@enderror
            </div>

            <div class="input-container">
                <label for="price">Цена, ₸</label>
                <input id="price" type="number" step="0.01" min="0" name="price" class="custom-input"
                       value="{{ old('price', $product->price) }}" required>
                @error('price')<div style="color:red;font-size:12px;">{{ $message }}</div>@enderror
            </div>

            <div class="input-container">
                <label for="quantity">Количество на складе</label>
                <input id="quantity" type="number" min="0" name="quantity" class="custom-input"
                       value="{{ old('quantity', optional($product->inventory)->quantity ?? 0) }}" required>
                @error('quantity')<div style="color:red;font-size:12px;">{{ $message }}</div>@enderror
            </div>

            <div class="input-container">
                <label>Свойства товара (например: Цвет, Размер)</label>

                <div id="properties-wrapper">
                    @php
                        $oldProperties = old('properties', $product->properties->map(fn($p) => [
                            'name' => $p->property_name,
                            'value' => $p->property_value,
                        ])->toArray());
                    @endphp

                    @if(!empty($oldProperties))
                        @foreach($oldProperties as $i => $prop)
                            <div class="phone-input-wrapper" style="margin-bottom:8px;">
                                <input type="text" name="properties[{{ $i }}][name]" class="custom-input"
                                       placeholder="Название (например, Цвет)"
                                       value="{{ $prop['name'] ?? '' }}"
                                       style="margin-right:5px;">
                                <input type="text" name="properties[{{ $i }}][value]" class="custom-input"
                                       placeholder="Значение (например, Красный)"
                                       value="{{ $prop['value'] ?? '' }}">
                            </div>
                        @endforeach
                    @else
                        <div class="phone-input-wrapper" style="margin-bottom:8px;">
                            <input type="text" name="properties[0][name]" class="custom-input"
                                   placeholder="Название (например, Цвет)"
                                   style="margin-right:5px;">
                            <input type="text" name="properties[0][value]" class="custom-input"
                                   placeholder="Значение (например, Красный)">
                        </div>
                    @endif
                </div>

                <button type="button"
                        class="order-button"
                        style="margin-top:10px;font-size:16px;padding:8px 12px;"
                        onclick="addPropertyRow()">
                    + Добавить свойство
                </button>
            </div>

            <div class="input-container">
                <label for="images">Фотографии товара</label>
                <input id="images" type="file" name="images[]" multiple>

                @if($product->images && $product->images->isNotEmpty())
                    <div style="margin-top:10px; display:flex; gap:10px; flex-wrap:wrap;">
                        @foreach($product->images as $img)
                            <div>
                                <img src="{{ asset('storage/' . $img->image_url) }}"
                                     alt=""
                                     style="width:80px;height:80px;object-fit:cover;border-radius:8px;">
                            </div>
                        @endforeach
                    </div>
                @endif

                @error('images.*')<div style="color:red;font-size:12px;">{{ $message }}</div>@enderror
            </div>

            <button type="submit" class="order-button">
                {{ $mode === 'create' ? 'Сохранить товар' : 'Обновить товар' }}
            </button>
        </form>
    </section>

    <script>
        let propIndex = {{ !empty($oldProperties) ? count($oldProperties) : 1 }};

        function addPropertyRow() {
            const wrapper = document.getElementById('properties-wrapper');
            const div = document.createElement('div');
            div.className = 'phone-input-wrapper';
            div.style.marginBottom = '8px';

            div.innerHTML = `
                <input type="text" name="properties[${propIndex}][name]" class="custom-input"
                       placeholder="Название (например, Цвет)" style="margin-right:5px;">
                <input type="text" name="properties[${propIndex}][value]" class="custom-input"
                       placeholder="Значение (например, Красный)">
            `;
            wrapper.appendChild(div);
            propIndex++;
        }
    </script>
@endsection
