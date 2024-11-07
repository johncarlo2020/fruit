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
            background-image: url('{{ asset('build/assets/images/HomePage.webp') }}');
            background-size: cover;
            background-position: center;
            position: relative;
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
        }
    </style>
    <body class="welcome-page">
        <a class="btn" href="{{ url('/screen2') }}">Start</a>
    </body>
</html>
