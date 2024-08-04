@extends('layouts.admin')

@section('content')
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <h1 class="text-xl font-bold mb-4">Заказы</h1>
    @if( isset($orders) )
        <h3 class="font-bold mb-4">{{ count($orders) }} активных заказов</h3>
    @else
        <h3 class="font-bold mb-4">Нет активных заказов</h3>
    @endif

    <div class="max-w-md mx-auto space-y-4" id="order-container">
        @foreach($orders as $order)
            <div class="order-card border rounded-lg shadow-lg bg-white">
                <div class="order-header flex justify-between items-center p-2.5 rounded-lg cursor-pointer">
                    <span class="order-status bg-red-500 text-white p-2 br-1 rounded-full">{{ __('status.'.$order->status) }}</span>
                    <span class="order-phone">{{ $order->user_phone }}</span>
                    <span class="order-toggle">v</span>
                </div>
                <div class="order-details hidden p-4 space-y-2">
                    <p class="font-bold">{{ $order->product->name }}</p>
                    <hr class="border-gray-300">
                    <p>Время заказа: {{ $order->created_at }}</p>
                    <hr class="border-gray-300">
                    <p>Желаемое время доставки: {{ $order->selected_time }}</p>
                    <hr class="border-gray-300">
                    <p>Адрес: {{ $order->city.', '.$order->region.', '.$order->address }}</p>
                    <hr class="border-gray-300">
                    <p>Надпись на открытке: {{ $order->card_message }}</p>
                    <hr class="border-gray-300">
                    <p>Способ оплаты: Удаленная оплата Kaspi. Выставить счет на номер {{ $order->user_phone }}</p>
                    @if($order->product)

                        <div class="open-button w-full bg-blue-500 text-white mt-2 rounded">
                            <a href="{{ route('public.show', $order->product->id) }}" class="block w-full text-center py-4">ОТКРЫТЬ КАРТОЧКУ ТОВАРА</a>
                        </div>
                    @else
                        <p class="text-red-500">Товар не найден</p>
                    @endif

                    <form action="{{ route('admin.orders.nextStage', ['order' => $order->id]) }}" method="POST">
                        @csrf
                        <button type="submit" class="next-stage-button w-full bg-blue-500 text-white py-4 mt-4 rounded" style="background-color: #10b981; color: white; padding-top: 1rem; padding-bottom: 1rem; margin-top: 2rem; border-radius: 0.25rem;">
                            ПЕРЕЙТИ К СЛЕДУЮЩЕЙ СТАДИИ
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>

    <script>
        document.getElementById('order-container').addEventListener('click', function(event) {
            if (event.target.closest('.order-header')) {
                const card = event.target.closest('.order-card');
                const details = card.querySelector('.order-details');
                const toggle = card.querySelector('.order-toggle');
                details.classList.toggle('hidden');
                toggle.textContent = details.classList.contains('hidden') ? 'v' : '>';
            }
        });
    </script>
@endsection
