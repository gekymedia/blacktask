<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-primary-light dark:text-primary-dark">
                <i class="fas fa-tasks mr-2"></i> BLACKTASK Dashboard
            </h1>
            <button id="theme-toggle" class="p-2 rounded-full bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                <i class="fas fa-moon dark:hidden"></i>
                <i class="fas fa-sun hidden dark:block"></i>
            </button>
        </div>
    </x-slot>

    <div class="container mx-auto px-4 py-8 max-w-7xl">
        <!-- Welcome Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 p-6 mb-8">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-primary-light dark:bg-primary-dark text-white mr-4">
                    <i class="fas fa-user text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-semibold">Welcome back, {{ Auth::user()->name }}!</h2>
                    <p class="text-gray-600 dark:text-gray-400">You have {{ Auth::user()->tasks()->count() }} total tasks</p>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Today's Tasks -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-gray-500 dark:text-gray-400">Today's Tasks</h3>
                        <p class="text-2xl font-bold">{{ Auth::user()->tasks()->whereDate('task_date', today())->count() }}</p>
                    </div>
                    <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300">
                        <i class="fas fa-calendar-day text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Completed Tasks -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-gray-500 dark:text-gray-400">Completed</h3>
                        <p class="text-2xl font-bold">{{ Auth::user()->tasks()->where('is_done', true)->count() }}</p>
                    </div>
                    <div class="p-3 rounded-full bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-300">
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Upcoming Tasks -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-gray-500 dark:text-gray-400">Upcoming</h3>
                        <p class="text-2xl font-bold">{{ Auth::user()->tasks()->whereDate('task_date', '>', today())->count() }}</p>
                    </div>
                    <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900 text-yellow-600 dark:text-yellow-300">
                        <i class="fas fa-clock text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Add New Task -->
            <a href="{{ route('tasks.index') }}" class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-all">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-primary-light dark:bg-primary-dark text-white mr-4">
                        <i class="fas fa-plus text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold">Create New Task</h3>
                        <p class="text-gray-600 dark:text-gray-400">Add a task to your list</p>
                    </div>
                </div>
            </a>

            <!-- View All Tasks -->
            <a href="{{ route('tasks.index') }}" class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-all">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900 text-purple-600 dark:text-purple-300 mr-4">
                        <i class="fas fa-list-check text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold">View All Tasks</h3>
                        <p class="text-gray-600 dark:text-gray-400">Manage your task list</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <script>
        // Theme handling
        document.addEventListener('DOMContentLoaded', function() {
            const themeToggle = document.getElementById('theme-toggle');
            
            if (localStorage.getItem('dark-mode') === 'true' || 
                (!localStorage.getItem('dark-mode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
                localStorage.setItem('dark-mode', 'true');
            }

            themeToggle.addEventListener('click', function() {
                document.documentElement.classList.toggle('dark');
                localStorage.setItem('dark-mode', document.documentElement.classList.contains('dark'));
            });
        });
    </script>
</x-app-layout>