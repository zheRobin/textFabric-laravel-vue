<!DOCTYPE html>
<html class="h-full bg-white" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <link rel="icon" href="favicon.svg" type="image/svg+xml"/>

        <!-- Fonts -->
        <link rel="preconnect" href="https://rsms.me">
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css">

        <!-- Scripts -->
        @routes

        @vite(['resources/js/app.js'])
        @inertiaHead
    </head>
    <body class="font-sans antialiased h-full">
        @inertia
    </body>
</html>
