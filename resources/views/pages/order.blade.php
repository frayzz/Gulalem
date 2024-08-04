@extends('layouts.app')

@section('content')
    <div id="success-message" class="fixed inset-0 bg-white flex items-center justify-center z-50" style="display: none;">
        <div class="text-center">
            <svg class="w-16 h-16 text-green-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <p class="mt-4 text-lg font-semibold" id="success-text">Заказ успешно оформлен!</p>
            <p class="text-lg font-semibold">Мы сейчас перенаправим вас в главное меню</p>
        </div>
    </div>
    @if(session('success'))
        <div id="success-message" class="fixed inset-0 bg-white flex items-center justify-center z-50">
            <div class="text-center">
                <svg class="w-16 h-16 text-green-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <p class="mt-4 text-lg font-semibold">{{ session('success') }}</p>
                <p class="text-lg font-semibold">Мы сейчас перенаправим вас в главное меню</p>
            </div>
        </div>
        <script>
            setTimeout(() => {
                document.getElementById('success-message').style.display = 'none';
                window.location.href = '{{ url('/') }}';
            }, 5000); // Перенаправление через 5 секунд
        </script>
    @endif

    <section>
        <div class="order">
            <div class="order-header">
                <h1>Страница оформления заказа</h1>
                <p>После отправки формы, вам перезвонит оператор и примет у вас оплату</p>
            </div>

            <form method="POST" class="post-red post-container menu-containers" action="{{ route('logout') }}">
                @csrf
                <img src="{{ url('images/logout.webp') }}" alt="" class="menu-icons">

                <a href="{{ route('logout') }}" slot="full-post-link" class="absolute-link" target="_self"
                   onclick="event.preventDefault();
                                                this.closest('form').submit();">exit</a>
            </form>



            <div class="product-container flex items-start">
                @if($product->images->isNotEmpty())
                    <picture>
                        @foreach($product->images as $image)
                            @if($image->format == 'thumbnail')
                                <source srcset="{{ asset('storage/' . $image->image_url) }}" type="image/{{ $image->type }}" media="(max-width: 600px)">
                            @elseif($image->format == 'medium')
                                <source srcset="{{ asset('storage/' . $image->image_url) }}" type="image/{{ $image->type }}" media="(max-width: 1200px)">
                            @elseif($image->format == 'large')
                                <source srcset="{{ asset('storage/' . $image->image_url) }}" type="image="{{ $image->type }}">
                            @endif
                        @endforeach
                        <img src="{{ asset('storage/' . $product->images->first()->image_url) }}" alt="{{ $product->name }}" class="w-24 h-24 object-cover">
                    </picture>
                @else
                    <img src="{{ asset('images/no-image.png') }}" alt="No image" class="w-24 h-24 object-cover">
                @endif
                <div class="product-info ml-4">
                    <h4>{{ $product->name }}</h4>
                    <div class="product-description">{{ $product->description }}</div>
                    <p>{{ $product->price }}</p>
                </div>
            </div>

            <form id="order-form" method="POST" action="{{ route('submit.order') }}">
                @csrf
                <div class="input-container">
                    <h3>Ваш номер телефона</h3>
                    <div class="phone-input-wrapper">
                        <span class="phone-prefix">+7</span>
                        <input type="text" class="custom-input phone-input @error('user_phone') border-red-500 bg-red-100 @enderror" name="user_phone" placeholder="(777) 445-45-75" value="{{ old('user_phone', Auth::user()->phone ?? '') }}" {{ Auth::check() ? 'readonly' : '' }}>
                        @error('user_phone')
                        <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="input-container">
                    <h3>Телефон получателя (не обязательно)</h3>
                    <div class="phone-input-wrapper">
                        <span class="phone-prefix">+7</span>
                        <input type="text" class="custom-input phone-input" name="recipient_phone" placeholder="(777) 445-45-75" value="{{ old('recipient_phone') }}">
                    </div>
                    <span><i>указывать, если вы заказываете не себе</i></span>
                    @error('recipient_phone')
                    <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <div class="input-container">
                    <h3>Адресс доставки</h3>
                    <div class="">
                        <select name="city" class="custom-select" disabled>
                            <option value="Алматы">г. Алматы</option>
                        </select>
                        <select class="custom-input" name="region" placeholder="Адресс доставки">
                            <option selected disabled hidden>Выберите район</option>
                            <option value="Алмалинский">Алмалинский р-н</option>
                            <option value="Наурызбайский">Наурызбайский р-н</option>
                            <option value="Жетысуский">Жетысуский р-н</option>
                            <option value="Туркисибский">Туркисибский р-н</option>
                            <option value="Бостандыкский">Бостандыкский р-н</option>
                            <option value="Алатауский">Алатауский р-н</option>
                            <option value="Ауэзовский">Ауэзовский р-н</option>
                        </select>
                        <input type="text" class="custom-input" name="address" placeholder="Адресс доставки" value="{{ old('address') }}">
                    </div>
                    @error('address')
                    <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <div class="input-container">
                    <h3>Выберите время доставки</h3>
                    <div class="delivery-options" id="delivery-times"></div>
                    <input type="hidden" id="selected-time" name="selected_time" value="{{ old('selected_time', 'ASAP') }}">
                    @error('selected_time')
                    <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <div class="input-container" id="custom-time-input" style="display: none;">
                    <h3>Выберите дату и время:</h3>
                    <input type="datetime-local" id="custom-time" name="custom_time" value="{{ old('custom_time') }}">
                    @error('custom_time')
                    <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <div class="input-container">
                    <h3>Надпись на открытке (не обязательно)</h3>
                    <textarea type="text" class="custom-input" name="card_message" placeholder="Надпись на открытке" value="{{ old('card_message') }}"></textarea>
                    @error('card_message')
                    <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <div class="input-container">
                    <h3>Способ оплаты</h3>
                    <select class="custom-select" name="payment_method">
                        <option value="call_manager" {{ old('payment_method') == 'call_manager' ? 'selected' : '' }}>Звонок менеджера ( Счет на Kaspi )</option>
                        <option value="bank_card" {{ old('payment_method') == 'bank_card' ? 'selected' : '' }}>Банковская карта</option>
                        <option value="kaspi_qr" disabled>Kaspi QR ( Скоро )</option>
                    </select>
                    @error('payment_method')
                    <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <input type="hidden" name="product_id" value="{{ $product->id }}">

                @if(Auth::check())
                    <button type="submit" class="order-button">Оформить заказ</button>
                @else
                    <button type="button" class="order-button" id="send-code-button">Отправить код</button>
                    <button type="button" class="order-button" id="resend-code-button" style="display: none;">Отправить код повторно</button>
                @endif
            </form>

            <form id="verification-form" method="POST" style="display: none;">
                @csrf
                <div class="input-container">
                    <h3>Введите код подтверждения</h3>
                    <input type="text" class="custom-input @error('verification_code') border-red-500 bg-red-100 @enderror" name="verification_code" placeholder="1234">
                    @error('verification_code')
                    <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>
                <button type="button" class="order-button" id="verify-code-button">Подтвердить</button>
            </form>
        </div>
    </section>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@endsection
