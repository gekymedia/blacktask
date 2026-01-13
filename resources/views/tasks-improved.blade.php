<!DOCTYPE html>
<html lang="en" class="{{ session('dark_mode', false) ? 'dark' : '' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>BLACKTASK - Daily Task Planner</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
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
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/luxon@3.0.1/build/global/luxon.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#000000">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    
    <!-- Favicons -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('web_pwa/icon-16x16.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('web_pwa/icon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="48x48" href="{{ asset('web_pwa/icon-48x48.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('web_pwa/icon-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('web_pwa/icon-512x512.png') }}">
    <link rel="apple-touch-icon" sizes="192x192" href="{{ asset('web_pwa/icon-192x192.png') }}">
    <link rel="apple-touch-icon" sizes="512x512" href="{{ asset('web_pwa/icon-512x512.png') }}">
    
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
    </style>
</head>

<body class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200 min-h-screen transition-colors duration-300">
    <div class="container mx-auto px-4 py-8 max-w-2xl">
        <!-- Header -->
        <header class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-primary-light dark:text-primary-dark">
                <i class="fas fa-tasks mr-2"></i> BLACKTASK
            </h1>
            <button id="theme-toggle" class="p-2 rounded-full bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                <i class="fas fa-moon dark:hidden"></i>
                <i class="fas fa-sun hidden dark:block"></i>
            </button>
        </header>

        <!-- Today's Date -->
        <div class="mb-6 text-center">
            <h2 class="text-xl font-semibold">
                <i class="far fa-calendar-alt mr-2"></i> Today - {{ now()->format('l, F j, Y') }}
            </h2>
        </div>

        <!-- Add Task Form -->
        <x-task-form :categories="auth()->user()->categories ?? collect([])" />

        <!-- Task List -->
        <div id="task-list" class="space-y-3">
            @forelse($tasks as $task)
                <x-task-item :task="$task" />
            @empty
                <x-empty-state />
            @endforelse
        </div>

        <!-- Notification -->
        <x-notification />
    </div>

    <script type="module" src="{{ asset('resources/js/tasks.js') }}"></script>
</body>
</html>

