@extends('layouts.app')

@section('title', 'Gulalem - твой цветочный мир')

@section('content')
    <section>
        <div class="order">
            <h1>Страница оформления заказа</h1>
            <p>После отправки формы, вам перезвонит оператор и примет у вас оплату</p>
            <input type="text" class="custom-input" placeholder="Ваш номер телефона">
            <input type="text" class="custom-input" placeholder="Адресс доставки">
            <input type="text" class="custom-input" placeholder="Желаемое время доставки">
            <button class="order-button">Заказать</button>
        </div>
    </section>
@endsection
