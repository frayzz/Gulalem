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
                        <select class="custom-input" name="region" placeholder="Адресс доставки" value="{{ old('address') }}">
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Форматирование номера телефона
            const phoneInputs = document.querySelectorAll('.phone-input');

            phoneInputs.forEach(function(phoneInput) {
                phoneInput.addEventListener('input', function() {
                    let value = phoneInput.value.replace(/\D/g, '');
                    if (value.length > 10) value = value.slice(0, 10);

                    let formattedValue = '';
                    if (value.length > 0) formattedValue += '(' + value.slice(0, 3);
                    if (value.length >= 4) formattedValue += ') ' + value.slice(3, 6);
                    if (value.length >= 7) formattedValue += '-' + value.slice(6, 8);
                    if (value.length >= 9) formattedValue += '-' + value.slice(8, 10);

                    phoneInput.value = formattedValue;
                });
            });

            // Выборка времени доставки
            const deliveryTimesContainer = document.getElementById('delivery-times');
            const currentTime = new Date();
            const interval = 120; // Интервал в минутах (2 часа)
            const numberOfSlots = 3; // Количество временных интервалов

            // Добавляем опцию "Как можно быстрее" и делаем её выбранной по умолчанию
            const asapDiv = document.createElement('div');
            asapDiv.className = 'delivery-option selected';
            asapDiv.setAttribute('data-time', 'fast');
            asapDiv.textContent = 'Как можно быстрее';
            deliveryTimesContainer.appendChild(asapDiv);
            document.getElementById('selected-time').value = 'ASAP';

            for (let i = 1; i <= numberOfSlots; i++) {
                const startTime = new Date(currentTime.getTime() + i * interval * 60000);
                const endTime = new Date(startTime.getTime() + interval * 60000);
                const startTimeStr = startTime.toTimeString().substring(0, 5);
                const endTimeStr = endTime.toTimeString().substring(0, 5);

                const timeSlotDiv = document.createElement('div');
                timeSlotDiv.className = 'delivery-option';
                timeSlotDiv.setAttribute('data-time', `${startTimeStr}-${endTimeStr}`);
                timeSlotDiv.textContent = `${startTimeStr} - ${endTimeStr}`;
                deliveryTimesContainer.appendChild(timeSlotDiv);
            }

            // Добавляем опцию "Выбрать свое время"
            const customTimeDiv = document.createElement('div');
            customTimeDiv.className = 'delivery-option';
            customTimeDiv.setAttribute('data-time', 'CUSTOM');
            customTimeDiv.textContent = 'Выбрать свое время';
            deliveryTimesContainer.appendChild(customTimeDiv);

            const deliveryOptions = document.querySelectorAll('.delivery-option');
            deliveryOptions.forEach(option => {
                option.addEventListener('click', function() {
                    deliveryOptions.forEach(opt => opt.classList.remove('selected'));
                    option.classList.add('selected');
                    const selectedTime = option.getAttribute('data-time');
                    document.getElementById('selected-time').value = selectedTime;
                    if (selectedTime === 'CUSTOM') {
                        document.getElementById('custom-time-input').style.display = 'block';
                        setCustomTimeConstraints();
                    } else {
                        document.getElementById('custom-time-input').style.display = 'none';
                    }
                });
            });

            // Показать поле ввода даты и времени, если была выбрана опция "CUSTOM"
            if (document.getElementById('selected-time').value === 'CUSTOM') {
                document.getElementById('custom-time-input').style.display = 'block';
            }

            function setCustomTimeConstraints() {
                const customTimeInput = document.getElementById('custom-time');
                const currentTime = new Date();
                const minTime = new Date(currentTime.getTime() + 2 * 60 * 60 * 1000); // Через 2 часа

                // Форматируем дату и время для атрибута min
                const year = minTime.getFullYear();
                const month = String(minTime.getMonth() + 1).padStart(2, '0');
                const day = String(minTime.getDate()).padStart(2, '0');
                const hours = String(minTime.getHours()).padStart(2, '0');
                const minutes = String(minTime.getMinutes()).padStart(2, '0');

                const minDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;
                customTimeInput.min = minDateTime;
            }

            // Обработка отправки и проверки кода
            const sendCodeButton = document.getElementById('send-code-button');
            const resendCodeButton = document.getElementById('resend-code-button');
            const verificationForm = document.getElementById('verification-form');
            const orderForm = document.getElementById('order-form');
            const verifyCodeButton = document.getElementById('verify-code-button');

            sendCodeButton.addEventListener('click', function() {
                sendVerificationCode();
            });

            resendCodeButton.addEventListener('click', function() {
                sendVerificationCode();
            });

            function sendVerificationCode() {
                const formData = new FormData(orderForm);
                fetch('{{ route('send.verification.code') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.message) {
                            alert(data.message);
                            orderForm.style.display = 'none';
                            verificationForm.style.display = 'block';
                            sendCodeButton.style.display = 'none';
                            resendCodeButton.style.display = 'block';
                        } else if (data.errors) {
                            alert('Ошибка: ' + data.errors.join(', '));
                        }
                    })
                    .catch(error => console.error('Ошибка:', error));
            }

            verifyCodeButton.addEventListener('click', function() {
                const formData = new FormData(verificationForm);
                formData.append('user_phone', document.querySelector('[name="user_phone"]').value);
                formData.append('recipient_phone', document.querySelector('[name="recipient_phone"]').value);
                formData.append('address', document.querySelector('[name="address"]').value);
                formData.append('selected_time', document.querySelector('[name="selected_time"]').value);
                formData.append('custom_time', document.querySelector('[name="custom_time"]').value);
                formData.append('card_message', document.querySelector('[name="card_message"]').value);
                formData.append('payment_method', document.querySelector('[name="payment_method"]').value);
                fetch('{{ route('verify.code') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.message && data.message.includes('успешно')) {
                            document.getElementById('success-text').innerText = data.message;
                            document.getElementById('success-message').style.display = 'flex';
                            setTimeout(() => {
                                window.location.href = '{{ url('/') }}';
                            }, 5000); // Перенаправление через 5 секунд
                        } else if (data.errors) {
                            alert('Ошибка: ' + data.errors.join(', '));
                        }
                    })
                    .catch(error => console.error('Ошибка:', error));
            });

        });
    </script>
@endsection
