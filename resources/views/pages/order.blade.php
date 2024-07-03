@extends('layouts.app')

@section('title', 'Gulalem - твой цветочный мир')

@section('content')
    <section>
        <div class="order">
            <h1>Страница оформления заказа</h1>
            <p>После отправки формы, вам перезвонит оператор и примет у вас оплату</p>
            <div class="input-container">
                <h3>Ваш номер телефона</h3>
                <input type="text" class="custom-input" placeholder="Ваш номер телефона">
            </div>
            <div class="input-container">
                <h3>Адресс доставки</h3>
                <input type="text" class="custom-input" placeholder="Адресс доставки">
            </div>
            <div class="input-container">
                <h3>Выберите время доставки</h3>
                <div class="delivery-options" id="delivery-times">
                    <div class="delivery-option" data-time="09:30-10:00">Как можно скорее</div>
                    <div class="delivery-option selected" data-time="09:45-10:15">09:45 - 10:15</div>
                    <div class="delivery-option" data-time="10:00-10:30">10:00 - 10:30</div>
                    <div class="delivery-option" data-time="10:15-10:45">10:15 - 10:45</div>
                    <!-- Добавьте больше вариантов времени здесь, если необходимо -->
                </div>
                <input type="hidden" id="selected-time" name="selected_time">
            </div>

            <button class="order-button">Оформить заказ</button>
        </div>
    </section>
@endsection
