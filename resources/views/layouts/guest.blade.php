<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Resource Africa') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            .logo-container {
                width: 200px;
                height: auto;
                display: block;
                margin-bottom: 1.5rem;
            }
            
            .logo-container img {
                max-width: 100%;
                height: auto;
            }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
            <div class="logo-container mb-6">
                <a href="/">
                    <x-application-logo />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-2 px-6 py-6 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg border border-gray-200">
                {{ $slot }}
            </div>
            
            <div class="mt-6 text-center text-sm text-gray-500">
                &copy; {{ date('Y') }} Resource Africa. All rights reserved.
            </div>
        </div>
    </body>
</html>
