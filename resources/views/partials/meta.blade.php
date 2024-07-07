<title>@yield('title')</title>
<meta name="description" content="Купить цветы и букеты в GULALEM. Популярные, классические, моно и смешанные букеты. Закажите сейчас!">
<meta name="keywords" content="цветы, букеты, купить цветы, GULALEM, классические букеты, моно-букеты, смешанные букеты">
<!-- Open Graph метатеги -->
<meta property="og:title" content="@yield('og_title', 'Gulalem - твой цветочный мир')">
<meta property="og:description" content="@yield('og_description', 'Самые свежие и красивые цветы только у нас!')">
<meta property="og:image" content="@yield('og_image', asset('images/default-og-image.jpg'))">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:type" content="website">
