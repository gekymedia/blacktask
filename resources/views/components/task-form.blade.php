@props(['categories' => []])

<form id="add-task-form" class="mb-8">
    @csrf
    <div class="flex gap-2 mb-4">
        <input
            type="text"
            id="task-input"
            name="title"
            placeholder="Add a new task..."
            class="flex-1 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-light dark:focus:ring-primary-dark transition-colors"
            required
            autocomplete="off">
        <button
            type="submit"
            class="px-4 py-2 bg-primary-light dark:bg-primary-dark text-white rounded-lg hover:bg-opacity-90 transition-colors">
            <i class="fas fa-plus mr-2"></i> Add
        </button>
    </div>
    
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Category</label>
            <select name="category_id" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2">
                <option value="">No category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" style="color: {{ $category->color }}">
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium mb-1">Priority</label>
            <select name="priority" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2">
                <option value="0">Low</option>
                <option value="1" selected>Medium</option>
                <option value="2">High</option>
            </select>
        </div>
    </div>
</form>

