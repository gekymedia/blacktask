<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-primary-light dark:text-primary-dark">
            <i class="fas fa-tasks mr-2"></i> Today's Tasks
        </h2>
    </x-slot>

    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <!-- Today's Date -->
        <div class="mb-6 text-center">
            <h2 class="text-xl font-semibold">
                <i class="far fa-calendar-alt mr-2"></i> {{ now()->format('l, F j, Y') }}
            </h2>
        </div>

        <!-- Add Task Form -->
        <x-task-form :categories="auth()->user()->categories ?? collect([])" />

        <!-- Task List -->
        <div id="task-list" class="space-y-3">
            @forelse($tasks as $task)
                <x-task-item :task="$task" />
            @empty
                <x-empty-state message="No tasks for today! Add one above." />
            @endforelse
        </div>

        <!-- Notification -->
        <x-notification />
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            // Add new task
            $('#add-task-form').submit(function(e) {
                e.preventDefault();
                const input = $('#task-input').val().trim();
                
                if (!input) return;

                const taskData = {
                    title: input,
                    task_date: new Date().toISOString().split('T')[0],
                    category_id: $('[name="category_id"]').val(),
                    priority: $('[name="priority"]').val(),
                    _token: '{{ csrf_token() }}'
                };

                $.ajax({
                    url: '{{ route('tasks.store') }}',
                    method: 'POST',
                    data: taskData,
                    success: function(response) {
                        $('#task-input').val('');
                        
                        const taskHtml = `
                            <div id="task-${response.id}" class="task-item bg-white dark:bg-gray-800 p-4 rounded-lg shadow hover:shadow-md transition-all flex items-center justify-between border border-gray-200 dark:border-gray-700">
                                <div class="flex items-center gap-3">
                                    <input type="checkbox" class="task-checkbox h-5 w-5 text-primary-light dark:text-primary-dark rounded focus:ring-primary-light dark:focus:ring-primary-dark border-gray-300 dark:border-gray-600 cursor-pointer" data-task-id="${response.id}">
                                    <div>
                                        <p class="font-medium">${escapeHtml(response.title)}</p>
                                        ${response.category ? `<p class="text-xs" style="color: ${response.category.color}"><i class="fas fa-tag"></i> ${response.category.name}</p>` : ''}
                                    </div>
                                </div>
                                <div class="flex gap-2 items-center">
                                    ${response.priority === 2 ? '<span class="text-red-500" title="High Priority"><i class="fas fa-exclamation-circle"></i></span>' : ''}
                                    <button class="reschedule-btn p-2 text-blue-500 hover:text-blue-700 dark:hover:text-blue-400 rounded-full hover:bg-blue-50 dark:hover:bg-gray-700 transition-colors" data-task-id="${response.id}" title="Move to tomorrow">
                                        <i class="far fa-calendar-plus"></i>
                                    </button>
                                    <button class="delete-btn p-2 text-red-500 hover:text-red-700 dark:hover:text-red-400 rounded-full hover:bg-red-50 dark:hover:bg-gray-700 transition-colors" data-task-id="${response.id}" title="Delete task">
                                        <i class="far fa-trash-alt"></i>
                                    </button>
                                </div>
                            </div>
                        `;

                        if ($('#task-list').find('.text-center').length) {
                            $('#task-list').html(taskHtml);
                        } else {
                            $('#task-list').prepend(taskHtml);
                        }

                        showNotification('Task added successfully!', 'success');
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr.responseText);
                        const errorMessage = xhr.responseJSON?.error || 'Failed to add task';
                        showNotification(errorMessage, 'error');
                    }
                });
            });

            // Toggle task completion
            $(document).on('change', '.task-checkbox', function() {
                const taskId = $(this).data('task-id');
                const isChecked = $(this).is(':checked');
                const checkbox = $(this);

                $.ajax({
                    url: `/tasks/${taskId}/toggle`,
                    method: 'PATCH',
                    data: {
                        _token: '{{ csrf_token() }}',
                        _method: 'PATCH'
                    },
                    success: function() {
                        checkbox.closest('.task-item').find('p').first().toggleClass('line-through opacity-50', isChecked);
                        showNotification(isChecked ? 'Task completed!' : 'Task marked as pending', 'success');
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr.responseText);
                        checkbox.prop('checked', !isChecked);
                        showNotification('Failed to update task', 'error');
                    }
                });
            });

            // Move task to tomorrow
            $(document).on('click', '.reschedule-btn', function() {
                const taskId = $(this).data('task-id');
                const button = $(this);

                $.ajax({
                    url: `/tasks/${taskId}/reschedule`,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function() {
                        $(`#task-${taskId}`).fadeOut(300, function() {
                            $(this).remove();

                            if ($('#task-list .task-item').length === 0) {
                                $('#task-list').html(`
                                    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                                        <i class="far fa-smile-beam text-4xl mb-2"></i>
                                        <p class="text-lg">No tasks for today! Add one above.</p>
                                    </div>
                                `);
                            }
                        });
                        
                        showNotification('Task moved to tomorrow!', 'success');
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr.responseText);
                        showNotification('Failed to reschedule task', 'error');
                    }
                });
            });

            // Delete task
            $(document).on('click', '.delete-btn', function() {
                const taskId = $(this).data('task-id');

                if (!confirm('Are you sure you want to delete this task?')) {
                    return;
                }

                $.ajax({
                    url: `/tasks/${taskId}`,
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function() {
                        $(`#task-${taskId}`).fadeOut(300, function() {
                            $(this).remove();

                            if ($('#task-list .task-item').length === 0) {
                                $('#task-list').html(`
                                    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                                        <i class="far fa-smile-beam text-4xl mb-2"></i>
                                        <p class="text-lg">No tasks for today! Add one above.</p>
                                    </div>
                                `);
                            }
                        });
                        
                        showNotification('Task deleted successfully!', 'error');
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr.responseText);
                        showNotification('Failed to delete task', 'error');
                    }
                });
            });

            // Helper function to escape HTML
            function escapeHtml(text) {
                const map = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                };
                return text.replace(/[&<>"']/g, m => map[m]);
            }

            // Helper function to show notifications
            function showNotification(message, type = 'success') {
                const notification = $('#notification');
                const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
                
                notification
                    .removeClass('bg-green-500 bg-red-500 hidden')
                    .addClass(`${bgColor} show`)
                    .text(message);

                setTimeout(() => {
                    notification.removeClass('show').addClass('hidden');
                }, 3000);
            }
        });
    </script>
    @endpush
</x-app-layout>

