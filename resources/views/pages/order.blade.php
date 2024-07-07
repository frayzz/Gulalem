@extends('layouts.app')



@section('content')
    <section>
        <div class="order">
            <h1>Страница оформления заказа</h1>
            <p>После отправки формы, вам перезвонит оператор и примет у вас оплату</p>
            <p>ВХОД ЧЕРЕЗ ГУГЛ</p>
            <p>или</p>
            <p>Заполните форму ниже</p>
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

            const deliveryOptions = document.querySelectorAll('.delivery-option');
            deliveryOptions.forEach(option => {
                option.addEventListener('click', function() {
                    deliveryOptions.forEach(opt => opt.classList.remove('selected'));
                    option.classList.add('selected');
                    document.getElementById('selected-time').value = option.getAttribute('data-time');
                });
            });
        });

        function submitDeliveryTime() {
            const selectedTime = document.getElementById('selected-time').value;
            if (selectedTime) {
                alert('Вы выбрали время доставки: ' + selectedTime);
                // Здесь можно отправить форму или выполнить другие действия
            } else {
                alert('Пожалуйста, выберите время доставки.');
            }
        }
    </script>
@endsection
