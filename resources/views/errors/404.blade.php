@extends('layouts.app')

@section('content')
    <div class="center-flexbox-elements">
        <div>
            <img src="{{ asset('images/Sad Emoticon.webp') }}" class="error-img" alt="">
            <h1>404</h1>
            <p>
                Если вы увидели данную страницу, <br>
                она не существует либо у нас ошибка. <br>
                Если вы напишете нам по кнопке внизу, <br>
                мы будем рады
            </p>
            <div class="feedback-button">
                <img src="{{ url('images/feedback.webp') }}" alt="" class="menu-icons">
                <div>
                    {{ __('Feedback') }}
                </div>
                <a slot="full-post-link" class="absolute-link" href="https://t.me/alexretr0" target="_self" aria-label=""></a>
            </div>
        </div>

    </div>
@endsection
