<title>@yield('title')</title>
<meta name="description" content="@yield('description')">
<meta name="keywords" content="цветы, букеты, купить цветы, GULALEM, классические букеты, моно-букеты, смешанные букеты">
<!-- Open Graph метатеги -->
<meta property="og:title" content="@yield('og_title', 'Gulalem - твой цветочный мир')">
<meta property="og:description" content="@yield('og_description', 'Gulalem - лучший интернет-магазин цветов. Заказывайте свежие букеты и цветочные композиции с доставкой по Алмате.')">
<meta property="og:image" content="@yield('og_image', asset('images/default-og-image.jpg'))">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:type" content="website">

<!-- Twitter Card мета-теги -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="@yield('og_title', 'Gulalem - твой цветочный мир')">
<meta name="twitter:description" content="@yield('og_description', 'Gulalem - лучший интернет-магазин цветов. Заказывайте свежие букеты и цветочные композиции с доставкой по Алмате.')">
<meta name="twitter:image" content="@yield('og_image', asset('images/default-twitter-image.png'))">
