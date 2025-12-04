<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ session('dark_mode', false) ? 'dark' : '' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Additional Resources from BLACKTASK -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
            .auth-card {
                transition: all 0.3s ease;
            }
            .auth-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            }
            .input-field {
                transition: all 0.2s ease;
            }
            .input-field:focus {
                border-color: '#4f46e5';
                box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.2);
            }
        </style>

        <!-- Original Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body class="font-sans text-gray-900 dark:text-gray-200 antialiased bg-gray-100 dark:bg-gray-900 min-h-screen transition-colors duration-300">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
            <!-- Theme Toggle Button -->
            <button id="theme-toggle" class="absolute top-4 right-4 p-2 rounded-full bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                <i class="fas fa-moon dark:hidden"></i>
                <i class="fas fa-sun hidden dark:block"></i>
            </button>

            <div class="mb-8">
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500 dark:text-gray-400" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg auth-card transition-all duration-300">
                {{ $slot }}
            </div>

            <!-- Notification Element -->
            <div id="notification" class="notification fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg hidden">
                <span id="notification-message"></span>
            </div>
        </div>

        <script>
            $(document).ready(function() {
                // Theme handling
                if (localStorage.getItem('dark-mode') === 'true' || 
                    (!localStorage.getItem('dark-mode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('dark-mode', 'true');
                }

                $('#theme-toggle').click(function() {
                    $('html').toggleClass('dark');
                    localStorage.setItem('dark-mode', $('html').hasClass('dark'));
                });

                // Enhanced input fields
                $('input, select, textarea').addClass('input-field bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 focus:border-primary-light dark:focus:border-primary-dark focus:ring-primary-light dark:focus:ring-primary-dark rounded-md shadow-sm');

                // Notification system
                window.showNotification = function(message, type = 'success') {
                    const notification = $('#notification');
                    const notificationMessage = $('#notification-message');
                    
                    notification.removeClass('bg-green-500 bg-red-500 bg-blue-500');
                    notification.addClass(
                        type === 'success' ? 'bg-green-500' :
                        type === 'error' ? 'bg-red-500' :
                        'bg-blue-500'
                    );
                    
                    notificationMessage.text(message);
                    notification.removeClass('hidden').addClass('show');
                    
                    setTimeout(() => {
                        notification.removeClass('show').addClass('hidden');
                    }, 3000);
                };
            });
        </script>
    </body>
</html>