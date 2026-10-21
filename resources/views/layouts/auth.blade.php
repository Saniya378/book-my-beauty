<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'BookMyBeauty')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    @yield('styles')
</head>

<body>
    <div class="auth">
        <div class="auth__media @yield('media-class', 'auth__media--login')">
            <a href="{{ route('home') }}" class="auth__back">
                <x-icon name="arrow-left" :size="15" />
                Back to home
            </a>

            <div>
                <span class="navbar__mark">BookMy<span>Beauty</span></span>
                <h1 class="auth__headline">@yield('headline')</h1>
                <p class="auth__text">@yield('intro')</p>

                @hasSection('points')
                    <ul class="auth__points">
                        @yield('points')
                    </ul>
                @endif
            </div>

            <p class="meta meta--light">
                <x-icon name="map-pin" :size="15" />
                Karakoram International University, Gilgit
            </p>
        </div>

        <div class="auth__form">
            @yield('form')
        </div>
    </div>

    @yield('scripts')
</body>

</html>