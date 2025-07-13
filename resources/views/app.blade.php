<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Amiqus ATS Demo') }}</title>

        <link rel="shortcut icon" href="/favicon--32.png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Dark mode initialization script -->
        <script>
            // Check for dark mode preference in localStorage
            const darkMode = localStorage.getItem('darkMode');

            // Apply dark mode only if saved preference is 'dark'
            if (darkMode === 'dark') {
                document.documentElement.classList.add('dark');
                document.documentElement.style.colorScheme = 'dark';
            } else {
                document.documentElement.classList.remove('dark');
                document.documentElement.style.colorScheme = 'light';
            }
        </script>

        <!-- Scripts and Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.ts'])
    </head>
    <body class="font-sans antialiased">
        <div id="app"></div>
    </body>
</html>
