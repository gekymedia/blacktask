<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ session('dark_mode', false) ? 'dark' : '' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- PWA Manifest -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#000000">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">

    <!-- Favicons -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon/blacktask_16x16.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon/blacktask_32x32.png') }}">
    <link rel="icon" type="image/png" sizes="48x48" href="{{ asset('favicon/blacktask_48x48.png') }}">
    <link rel="icon" type="image/png" sizes="64x64" href="{{ asset('favicon/blacktask_64x64.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('pwa/blacktask_192x192.png') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('pwa/blacktask_512x512.png') }}">
    <link rel="apple-touch-icon" sizes="192x192" href="{{ asset('mobile/blacktask_192x192.png') }}">
    <link rel="apple-touch-icon" sizes="512x512" href="{{ asset('mobile/blacktask_512x512.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Additional Resources from BLACKTASK -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chrono-node@2.3.5/dist/chrono.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            light: '#4f46e5',
                            dark: '#6366f1'
                        }
                    }
                }
            }
        }
    </script>

    <style>
        .task-item {
            transition: all 0.2s ease;
        }
        .task-item:hover {
            transform: translateY(-2px);
        }
        .completed {
            text-decoration: line-through;
            opacity: 0.7;
        }
        .notification {
            transition: all 0.3s ease;
            transform: translateY(20px);
            opacity: 0;
        }
        .notification.show {
            transform: translateY(0);
            opacity: 1;
        }
    </style>

    <!-- Original Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200 min-h-screen transition-colors duration-300">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white dark:bg-gray-800 shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main class="container mx-auto px-4 py-8 max-w-7xl">
            {{ $slot }}
        </main>

        <!-- Notification Element -->
        <div id="notification" class="notification fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg hidden">
            Task moved to tomorrow!
        </div>
    </div>

    <!-- Page-specific scripts -->
    @stack('scripts')

    <script>
        // Theme handling - Load immediately
        (function() {
            if (localStorage.getItem('dark-mode') === 'true' || 
                (!localStorage.getItem('dark-mode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
                localStorage.setItem('dark-mode', 'true');
            }
        })();

        // Initialize after DOM is ready
        $(document).ready(function() {
            // Theme toggle handler
            $(document).on('click', '#theme-toggle', function() {
                document.documentElement.classList.toggle('dark');
                const isDark = document.documentElement.classList.contains('dark');
                localStorage.setItem('dark-mode', isDark);
            });

            // Natural language processing (can be used in task forms)
            window.parseNaturalLanguage = function(input) {
                if (typeof chrono !== 'undefined') {
                    const result = chrono.parse(input)[0];
                    if (!result) return { title: input, date: null };
                    
                    const date = result.start.date();
                    const title = input.replace(result.text, '').trim();
                    
                    return { title, date };
                }
                return { title: input, date: null };
            };
        });
    </script>
</body>
</html>