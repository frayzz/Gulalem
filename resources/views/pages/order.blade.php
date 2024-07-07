@extends('layouts.app')



@section('content')
    <section>
        <div class="order">
            <h1>Страница оформления заказа</h1>
            <p>После отправки формы, вам перезвонит оператор и примет у вас оплату</p>
            <div class="product-container flex items-start">
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
                <div class="product-info ml-4">
                    <h4>{{ $product->name }}</h4>
                    <p class="product-description">{{ $product->description }}</p>
                    <p>{{ $product->price }}</p>
                </div>
            </div>
            <!--
            <p>ВХОД ЧЕРЕЗ ГУГЛ</p>
            <p>или</p>
            <p>Заполните форму ниже</p>
            -->
            <div class="input-container">
                <h3>Ваш номер телефона</h3>
                <input type="text" class="custom-input" placeholder="Ваш номер телефона">
            </div>
            <div class="input-container">
                <h3>Телефон получателя (не обязательно)</h3>
                <input type="text" class="custom-input" placeholder="Телефон получателя">
            </div>
            <div class="input-container">
                <h3>Адресс доставки</h3>
                <input type="text" class="custom-input" placeholder="Адресс доставки">
            </div>
            <div class="input-container">
                <h3>Выберите время доставки</h3>
                <div class="delivery-options" id="delivery-times"></div>
                <input type="hidden" id="selected-time" name="selected_time">
            </div>
            <div class="input-container" id="custom-time-input">
                <h3>Выберите дату и время:</h3>
                <input type="datetime-local" id="custom-time" name="custom_time">
            </div>
            <div class="input-container">
                <h3>Надпись на открытке (не обязательно)</h3>
                <input type="text" class="custom-input" placeholder="Надпись на открытке">
            </div>
            <div class="input-container">
                <h3>Способ оплаты</h3>
                <select class="custom-select">
                    <option value="" selected>Звонок менеджера ( Счет на Kaspi )</option>
                    <option value="">Банковская карта</option>
                    <option value="" disabled>Kaspi QR ( Скоро )</option>
                </select>
            </div>

            <button class="order-button" onclick="submitDeliveryTime()">Оформить заказ</button>
        </div>
    </section>
    <script>

        document.addEventListener('DOMContentLoaded', function() {
            const deliveryTimesContainer = document.getElementById('delivery-times');
            const currentTime = new Date();
            const interval = 120; // Интервал в минутах (2 часа)
            const numberOfSlots = 3; // Количество временных интервалов

            // Добавляем опцию "Как можно быстрее" и делаем её выбранной по умолчанию
            const asapDiv = document.createElement('div');
            asapDiv.className = 'delivery-option selected';
            asapDiv.setAttribute('data-time', 'ASAP');
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
        });

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

        function submitDeliveryTime() {
            const selectedTime = document.getElementById('selected-time').value;
            if (selectedTime) {
                if (selectedTime === 'CUSTOM') {
                    const customTime = document.getElementById('custom-time').value;
                    if (customTime) {
                        alert('Вы выбрали свое время доставки: ' + customTime);
                        // Здесь можно отправить форму или выполнить другие действия
                    } else {
                        alert('Пожалуйста, выберите дату и время.');
                    }
                } else {
                    alert('Вы выбрали время доставки: ' + selectedTime);
                    // Здесь можно отправить форму или выполнить другие действия
                }
            } else {
                alert('Пожалуйста, выберите время доставки.');
            }
        }
    </script>
@endsection
