<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
        @endif
    </head>
    <style>
        .welcome-page {
            width: 100%;
            height: 100vh;
            background-image: url('{{ Vite::asset('resources/images/HomePage.webp') }}');
            background-size: cover;
            background-position: center;
            position: relative;
            transition: 0.5s;
        }

        .welcome-page {
            cursor: url('{{ Vite::asset('resources/images/cursor.png') }}'), auto;
        }

        .btn {
            display: block;
            padding: 15px 90px;
            margin: 20px;
            background-color: #FFEEB8;
            color: #000000;
            text-decoration: none;
            border-radius: 5px;
            position: absolute;
            bottom: 20%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 100px,200px;
            text-align: center;
            font-size: 30px;
            font-weight: 700;
            box-shadow: 0 0 10px 0 rgba(0, 0, 0, 0.1);
            border: none;
            cursor: pointer;
            outline: none;
        }

        .btn::after {
            content: '';
            display: block;
            position: absolute;
            bottom: 0;
            right: 0;
            width: 100%;
            height: 100%;
            /* background-image: url('{{ Vite::asset('resources/images/') }}'); */
            z-index: -1;
            transition: 0.5s;
        }
    </style>
    <body class="welcome-page">
        <button class="btn">Start</button>
    </body>

    <script>
        var QRmode = false;
        const page = document.querySelector('.welcome-page');
        const btn = document.querySelector('.btn');

        const flow = ['welcome', 'howtoplay', 'scanqr', 'countdown'];

        let currentFlow = 0;

        page.addEventListener('click', () => {
            if (currentFlow === 0) {
                page.style.backgroundImage = 'url({{ Vite::asset('resources/images/Howtoplay.webp') }})';
                btn.style.display = 'none';
                currentFlow++;
            } else if (currentFlow === 1) {
                page.style.backgroundImage = 'url({{ Vite::asset('resources/images/ScanQR.webp') }})';
                currentFlow++;
            } else if (currentFlow === 2) {
                page.style.backgroundImage = 'url({{ Vite::asset('resources/images/Background.webp') }})';

                const countdown = document.createElement('div');
                countdown.style.position = 'absolute';
                countdown.style.top = '50%';
                countdown.style.left = '50%';

                let count = 3;
                countdown.innerText = count;
                countdown.style.fontSize = '100px';
                countdown.style.fontWeight = '700';
                countdown.style.transform = 'translate(-50%, -50%)';
                countdown.style.color = '#000000';

                page.appendChild(countdown);

                const interval = setInterval(() => {
                    count--;
                    countdown.innerText = count;

                    if (count === 0) {
                        clearInterval(interval);
                        window.location.href = '{{ url('/game') }}';
                        countdown.style.display = 'none';
                    }
                }, 1000);
            }
        });
    </script>
</html>
