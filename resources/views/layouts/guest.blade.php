<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>WebGis</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
</head>

<body
    style="background-image: url('/assets/img/bg-main.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat;"
    class="font-sans text-gray-900 antialiased">

    <!-- Overlay transparan -->
    <div style="position: fixed; inset: 0; background-color: rgba(77, 128, 141, 0.829); z-index: 0;"></div>

    <div class="min-vh-100 d-flex flex-column justify-content-center align-items-center"
        style="position: relative; z-index: 1;">
        <!-- Logo -->
        <div class="mb-4">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </div>

        <!-- Content -->
        <div class="card shadow-sm w-100"
            style="max-width: 24rem; background-color: rgba(2, 32, 65, 0.9); border: none; color: white;">
            <div class="card-body">
                {{ $slot }}
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
