@extends('layouts.app')

@section('title', 'Gulalem - твой цветочный мир')
@section('description', 'Gulalem - лучший интернет-магазин цветов. Заказывайте свежие букеты и цветочные композиции с доставкой по Алмате.')

@section('structured_data')
    <script type="application/ld+json">
        {
          "@context": "https://schema.org",
          "@type": "Store",
          "name": "Gulalem",
          "description": "Лучший интернет-магазин цветов. Заказывайте свежие букеты и цветочные композиции с доставкой по Алмате.",
          "image": "{{ secure_asset('images/favicon.ico') }}",
          "url": "{{ url('/') }}",
          "address": {
            "@type": "PostalAddress",
            "addressLocality": "Алматы",
            "addressRegion": "KZ",
            "streetAddress": "Толе би",
            "postalCode": "050000",
            "addressCountry": "KZ"
          }
}
    </script>

    <script type="application/ld+json">
        {
          "@context": "https://schema.org",
          "@type": "ItemList",
          "itemListElement": [
        @foreach($products as $index => $product)
            {
              "@type": "Product",
              "position": {{ $index + 1 }},
              "name": "{{ $product->name }}",
              "image": "{{ secure_asset('storage/' . $product->images->first()->image_url) }}",
              "description": "{{ $product->description }}",
              "offers": {
                "@type": "Offer",
                "url": "{{ route('public.show', ['slug' => $product->slug]) }}",
                "priceCurrency": "KZT",
                "price": "{{ $product->price }}",
                "availability": "https://schema.org/InStock",
                "itemCondition": "https://schema.org/NewCondition"
              }
            }@if(!$loop->last),@endif
        @endforeach
        ]
      }
    </script>
@endsection

@section('content')
    <section class="bg-white mt-12 rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.05)] p-6 md:p-10">
        <div class="flex flex-col gap-6 md:gap-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h2 class="text-2xl md:text-3xl font-semibold text-gray-900 mb-2">
                        Gulalem для цветочных магазинов
                    </h2>
                    <p class="text-gray-600 leading-relaxed">
                        Подключите свой магазин к Gulalem и получайте онлайн-заказы на букеты без вложений в разработку сайта.
                    </p>
                </div>

                {{-- Цифры / короткая статистика --}}
                <div class="grid grid-cols-2 gap-3 md:gap-4 md:grid-cols-3 text-center">
                    <div class="stat-card">
                        <div class="stat-card__value">0₸</div>
                        <div class="stat-card__label">подключение и размещение</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card__value">10–15 мин</div>
                        <div class="stat-card__label">чтобы отправить заявку</div>
                    </div>
                    <div class="stat-card md:col-span-1 col-span-2">
                        <div class="stat-card__value">200+ букетов</div>
                        <div class="stat-card__label">уже в каталоге Gulalem*</div>
                    </div>
                </div>
            </div>

            {{-- Для кого / что даёт --}}
            <div class="grid md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <h3 class="text-xl font-semibold text-gray-900">
                        Для кого это подходит
                    </h3>
                    <ul class="space-y-3">
                        <li class="flex gap-3 items-start">
                            <span class="check-mark mt-1"></span>
                            <span class="text-gray-700">
                            Небольшие цветочные салоны без собственного сайта.
                        </span>
                        </li>
                        <li class="flex gap-3 items-start">
                            <span class="check-mark mt-1"></span>
                            <span class="text-gray-700">
                            Магазины у дома, которые хотят принимать заказы не только по телефону.
                        </span>
                        </li>
                        <li class="flex gap-3 items-start">
                            <span class="check-mark mt-1"></span>
                            <span class="text-gray-700">
                            Действующие студии флористики, которые хотят дополнительный канал продаж.
                        </span>
                        </li>
                    </ul>

                    <h3 class="text-xl font-semibold text-gray-900 mt-4">
                        Что даёт размещение на Gulalem
                    </h3>
                    <ul class="space-y-3">
                        <li class="flex gap-3 items-start">
                            <span class="check-mark mt-1"></span>
                            <span class="text-gray-700">
                            Онлайн-витрина ваших букетов: названия, цены, фото и описания.
                        </span>
                        </li>
                        <li class="flex gap-3 items-start">
                            <span class="check-mark mt-1"></span>
                            <span class="text-gray-700">
                            Страница магазина с логотипом, адресом и контактами.
                        </span>
                        </li>
                        <li class="flex gap-3 items-start">
                            <span class="check-mark mt-1"></span>
                            <span class="text-gray-700">
                            Заказы через сайт или напрямую в WhatsApp — клиент сразу пишет вам.
                        </span>
                        </li>
                        <li class="flex gap-3 items-start">
                            <span class="check-mark mt-1"></span>
                            <span class="text-gray-700">
                            Дополнительный поток клиентов за счёт общего трафика Gulalem.
                        </span>
                        </li>
                    </ul>
                </div>

                {{-- Блок "Как это работает" и WhatsApp --}}
                <div class="flex flex-col gap-5">
                    <div class="bg-slate-50 border border-slate-200 rounded-2xl p-5 md:p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">
                            Как это работает
                        </h3>
                        <ol class="space-y-3 text-gray-700 list-decimal list-inside">
                            <li>Вы отправляете заявку в WhatsApp с названием магазина и контактом.</li>
                            <li>Мы создаём страницу магазина на Gulalem и помогаем оформить каталог.</li>
                            <li>Ваши букеты появляются в общем каталоге и на вашей странице.</li>
                            <li>Клиенты находят вас на Gulalem и оформляют заказ.</li>
                            <li>Вы получаете заказ и связываетесь с клиентом напрямую.</li>
                        </ol>
                    </div>

                    <div class="bg-green-50 border border-green-200 rounded-2xl p-5 md:p-6">
                        <p class="text-lg md:text-xl text-green-700 font-semibold mb-2">
                            Размещение каталога на Gulalem для магазинов — бесплатно.
                        </p>
                        <p class="text-gray-700 mb-4">
                            Нет платы за подключение и нет абонентской платы. Мы зарабатываем только на комиссии с успешных заказов*.
                        </p>
                        <p class="text-gray-500 text-xs mb-4">
                            *Точные условия сотрудничества можно уточнить при подключении.
                        </p>

                        <p class="text-gray-800 font-medium mb-2">
                            Написать нам в WhatsApp для подключения магазина:
                        </p>
                        <a href="https://wa.me/77085056789"
                           target="_blank"
                           rel="noopener"
                           class="inline-flex items-center justify-center whatsapp-btn">
                            <span>Написать в WhatsApp</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
        .stat-card {
            @apply bg-slate-50 rounded-xl px-3 py-3 md:px-4 md:py-4 flex flex-col justify-center;
        }

        .stat-card__value {
            @apply text-base md:text-lg font-semibold text-gray-900;
        }

        .stat-card__label {
            @apply text-[11px] md:text-xs text-gray-500 leading-snug;
        }

        .check-mark {
            width: 18px;
            height: 18px;
            display: inline-block;
            background-color: #4ade80; /* зелёный */
            border-radius: 4px;
            position: relative;
            flex-shrink: 0;
        }

        .check-mark::after {
            content: "";
            position: absolute;
            top: 3px;
            left: 5px;
            width: 6px;
            height: 10px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        .whatsapp-btn {
            background-color: #128C7E;
            color: #fff;
            border-radius: 9999px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            font-size: 0.95rem;
            text-decoration: none;
            transition: transform 0.15s ease, box-shadow 0.15s ease, background-color 0.15s ease;
            box-shadow: 0 10px 25px rgba(18, 140, 126, 0.25);
        }

        .whatsapp-btn:hover {
            background-color: #0f6b60;
            transform: translateY(-1px);
            box-shadow: 0 14px 30px rgba(18, 140, 126, 0.35);
        }

    </style>
@endsection
