/**
 * Task Manager - Handles all task-related operations
 */

class TaskManager {
    constructor(csrfToken) {
        this.csrfToken = csrfToken;
        this.init();
    }

    init() {
        this.bindEventListeners();
    }

    bindEventListeners() {
        // Add task form submission
        $(document).on('submit', '#add-task-form', (e) => this.handleAddTask(e));

        // Toggle task completion
        $(document).on('change', '.task-checkbox', (e) => this.handleToggleTask(e));

        // Reschedule task
        $(document).on('click', '.reschedule-btn', (e) => this.handleRescheduleTask(e));

        // Delete task
        $(document).on('click', '.delete-btn', (e) => this.handleDeleteTask(e));
    }

    async handleAddTask(e) {
        e.preventDefault();
        
        const input = $('#task-input').val().trim();
        if (!input) return;

        const parsed = this.parseNaturalLanguage(input);
        
        const taskData = {
            title: parsed.title,
            task_date: parsed.date ? parsed.date.toISOString().split('T')[0] : new Date().toISOString().split('T')[0],
            category_id: $('[name="category_id"]').val(),
            priority: $('[name="priority"]').val(),
            _token: this.csrfToken
        };

        try {
            const response = await $.ajax({
                url: '/tasks',
                method: 'POST',
                data: taskData
            });

            $('#task-input').val('');
            this.addTaskToDOM(response);
            this.showNotification('Task added successfully!', 'success');
        } catch (xhr) {
            console.error('Error:', xhr.responseText);
            const errorMessage = xhr.responseJSON?.error || 'Failed to add task';
            this.showNotification(errorMessage, 'error');
        }
    }

    async handleToggleTask(e) {
        const taskId = $(e.target).data('task-id');
        const isChecked = $(e.target).is(':checked');

        try {
            await $.ajax({
                url: `/tasks/${taskId}/toggle`,
                method: 'PATCH',
                data: {
                    _token: this.csrfToken,
                    _method: 'PATCH'
                }
            });

            $(`#task-${taskId} span`).toggleClass('completed', isChecked);
        } catch (xhr) {
            console.error('Error:', xhr.responseText);
            $(e.target).prop('checked', !isChecked); // Revert checkbox
            const errorMessage = xhr.responseJSON?.error || 'Failed to toggle task';
            this.showNotification(errorMessage, 'error');
        }
    }

    async handleRescheduleTask(e) {
        const taskId = $(e.currentTarget).data('task-id');

        try {
            await $.ajax({
                url: `/tasks/${taskId}/reschedule`,
                method: 'POST',
                data: {
                    _token: this.csrfToken,
                    _method: 'POST'
                }
            });

            this.removeTaskFromDOM(taskId);
            this.showNotification('Task moved to tomorrow!', 'success');
        } catch (xhr) {
            console.error('Error:', xhr.responseText);
            const errorMessage = xhr.responseJSON?.error || 'Failed to reschedule task';
            this.showNotification(errorMessage, 'error');
        }
    }

    async handleDeleteTask(e) {
        const taskId = $(e.currentTarget).data('task-id');

        if (!confirm('Are you sure you want to delete this task?')) {
            return;
        }

        try {
            await $.ajax({
                url: `/tasks/${taskId}`,
                method: 'DELETE',
                data: {
                    _token: this.csrfToken,
                    _method: 'DELETE'
                }
            });

            this.removeTaskFromDOM(taskId);
            this.showNotification('Task deleted successfully!', 'success');
        } catch (xhr) {
            console.error('Error:', xhr.responseText);
            const errorMessage = xhr.responseJSON?.error || 'Failed to delete task';
            this.showNotification(errorMessage, 'error');
        }
    }

    parseNaturalLanguage(input) {
        if (typeof luxon === 'undefined') {
            return { title: input, date: null };
        }

        const today = luxon.DateTime.now();
        const tomorrow = today.plus({ days: 1 });
        
        const patterns = [
            { test: /today/i, date: today },
            { test: /tomorrow/i, date: tomorrow },
            { test: /next week/i, date: today.plus({ weeks: 1 }) },
            { test: /next month/i, date: today.plus({ months: 1 }) }
        ];
        
        let date = null;
        let title = input;
        
        for (const pattern of patterns) {
            if (pattern.test.test(input)) {
                date = pattern.date;
                title = input.replace(pattern.test, '').trim();
                break;
            }
        }
        
        return { 
            title: title || input,
            date: date ? date.toJSDate() : null 
        };
    }

    addTaskToDOM(task) {
        const taskHtml = `
            <div id="task-${task.id}" class="task-item bg-white dark:bg-gray-800 p-4 rounded-lg shadow hover:shadow-md transition-all flex items-center justify-between border border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <input type="checkbox" class="task-checkbox h-5 w-5 text-primary-light dark:text-primary-dark rounded focus:ring-primary-light dark:focus:ring-primary-dark border-gray-300 dark:border-gray-600 cursor-pointer" data-task-id="${task.id}">
                    <span class="ml-3">${this.escapeHtml(task.title)}</span>
                </div>
                <div class="flex gap-2">
                    <button class="reschedule-btn p-2 text-blue-500 hover:text-blue-700 dark:hover:text-blue-400 rounded-full hover:bg-blue-50 dark:hover:bg-gray-700 transition-colors" data-task-id="${task.id}" title="Move to tomorrow">
                        <i class="far fa-calendar-plus"></i>
                    </button>
                    <button class="delete-btn p-2 text-red-500 hover:text-red-700 dark:hover:text-red-400 rounded-full hover:bg-red-50 dark:hover:bg-gray-700 transition-colors" data-task-id="${task.id}" title="Delete task">
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
    }

    removeTaskFromDOM(taskId) {
        $(`#task-${taskId}`).fadeOut(300, function() {
            $(this).remove();

            if ($('#task-list').children().length === 0) {
                $('#task-list').html(`
                    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                        <i class="far fa-smile-beam text-4xl mb-2"></i>
                        <p class="text-lg">No tasks for today! Add one above.</p>
                    </div>
                `);
            }
        });
    }

    showNotification(message, type = 'success') {
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

    escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }
}

export default TaskManager;

