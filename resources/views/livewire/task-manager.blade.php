<div class="max-w-7xl mx-auto p-6">
    <!-- Success Message -->
    @if (session()->has('message'))
        <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 px-4 py-3 rounded-lg flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            {{ session('message') }}
        </div>
    @endif

    <!-- Error Message -->
    @if (session()->has('error'))
        <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 px-4 py-3 rounded-lg flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    <!-- Form Validation Error -->
    @error('form')
        <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 px-4 py-3 rounded-lg flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            {{ $message }}
        </div>
    @enderror

    <!-- Header with Actions -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8 space-y-4 lg:space-y-0">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                My Tasks
            </h1>
            <p class="text-gray-600 dark:text-gray-400">
                Stay organized and boost your productivity
            </p>
        </div>
        
        <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
            <button 
                wire:click="toggleCategoryForm" 
                class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg shadow-sm hover:shadow-md transform hover:scale-105 transition-all duration-200 flex items-center justify-center"
            >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                </svg>
                Add Category
            </button>
            <button 
                wire:click="toggleCreateForm" 
                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-sm hover:shadow-md transform hover:scale-105 transition-all duration-200 flex items-center justify-center"
            >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add New Task
            </button>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow duration-200">
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Total Tasks</div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow duration-200">
            <div class="text-center">
                <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['completed'] }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Completed</div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow duration-200">
            <div class="text-center">
                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['pending'] }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Pending</div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow duration-200">
            <div class="text-center">
                <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $stats['in_progress'] }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">In Progress</div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow duration-200">
            <div class="text-center">
                <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $stats['overdue'] }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Overdue</div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <div class="flex flex-col lg:flex-row space-y-4 lg:space-y-0 lg:space-x-4">
            <!-- Search -->
            <div class="flex-1">
                <input 
                    wire:model.live="searchQuery"
                    type="text" 
                    placeholder="Search tasks..."
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-colors duration-200"
                >
            </div>

            <!-- Filters -->
            <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
                <!-- Status Filter -->
                <select wire:model.live="statusFilter" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    <option value="all">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                </select>

                <!-- Priority Filter -->
                <select wire:model.live="priorityFilter" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    <option value="all">All Priority</option>
                    <option value="low">Low</option>
                    <option value="normal">Normal</option>
                    <option value="high">High</option>
                    <option value="critical">Critical</option>
                </select>

                <!-- Category Filter -->
                <select wire:model.live="categoryFilter" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    <option value="all">All Categories</option>
                    <option value="none">No Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }} ({{ $category->tasks_count }})</option>
                    @endforeach
                </select>

                <!-- Clear Filters -->
                <button 
                    wire:click="clearFilters"
                    class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors duration-200 whitespace-nowrap"
                >
                    Clear Filters
                </button>
            </div>
        </div>
    </div>

    <!-- Create Category Form -->
    @if($showCategoryForm)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Create New Category</h3>
                <button wire:click="toggleCategoryForm" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form wire:submit="createCategory" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2">
                        <label for="categoryName" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category Name</label>
                        <input 
                            wire:model="categoryName" 
                            type="text" 
                            id="categoryName"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            placeholder="e.g., Work, Personal, Shopping"
                        >
                        @error('categoryName') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label for="categoryColor" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Color</label>
                        <input 
                            wire:model="categoryColor" 
                            type="color" 
                            id="categoryColor"
                            class="w-full h-10 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 cursor-pointer"
                        >
                        @error('categoryColor') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" wire:click="toggleCategoryForm" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                        Cancel
                    </button>
                    <button 
                        type="submit"
                        class="px-6 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors duration-200 disabled:opacity-50"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove>Create Category</span>
                        <span wire:loading>Creating...</span>
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- Create/Edit Task Form -->
    @if($showCreateForm)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 mb-8">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ $editingTaskId ? 'Edit Task' : 'Create New Task' }}
                </h3>
                <button wire:click="cancelEdit" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form wire:submit="{{ $editingTaskId ? 'updateTask' : 'createTask' }}" class="space-y-6">
                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Task Title</label>
                    <input 
                        wire:model="title" 
                        type="text" 
                        id="title"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-colors duration-200"
                        placeholder="What needs to be done?"
                    >
                    @error('title') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description (Optional)</label>
                    <textarea 
                        wire:model="description" 
                        id="description"
                        rows="3"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-colors duration-200"
                        placeholder="Add more details about this task..."
                    ></textarea>
                    @error('description') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Priority, Due Date, and Category Row -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Priority -->
                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Priority</label>
                        <select 
                            wire:model="priority" 
                            id="priority"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-colors duration-200"
                        >
                            <option value="low">Low</option>
                            <option value="normal">Normal</option>
                            <option value="high">High</option>
                            <option value="critical">Critical</option>
                        </select>
                        @error('priority') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Due Date -->
                    <div>
                        <label for="due_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Due Date</label>
                        <input 
                            wire:model="due_date" 
                            type="datetime-local" 
                            id="due_date"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-colors duration-200"
                        >
                        @error('due_date') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Category -->
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category</label>
                        <select 
                            wire:model="category_id" 
                            id="category_id"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-colors duration-200"
                        >
                            <option value="">No Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button 
                        type="button" 
                        wire:click="cancelEdit"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200"
                    >
                        Cancel
                    </button>
                    <button 
                        type="submit"
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200 disabled:opacity-50"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove>{{ $editingTaskId ? 'Update Task' : 'Create Task' }}</span>
                        <span wire:loading>{{ $editingTaskId ? 'Updating...' : 'Creating...' }}</span>
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- Tasks List -->
    <div class="space-y-4">
        @if($tasks->count() > 0)
            @foreach($tasks as $task)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-all duration-200 group">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start space-x-4 flex-1">
                            <!-- Checkbox -->
                            <button 
                                wire:click="toggleTask({{ $task->id }})"
                                class="mt-1 w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all duration-200
                                    {{ $task->status === 'completed' ? 'bg-green-500 border-green-500' : 'border-gray-300 dark:border-gray-600 hover:border-green-400' }}
                                    {{ $task->status === 'in_progress' ? 'bg-blue-500 border-blue-500' : '' }}"
                            >
                                @if($task->status === 'completed')
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                @elseif($task->status === 'in_progress')
                                    <div class="w-2 h-2 bg-white rounded-full"></div>
                                @endif
                            </button>

                            <!-- Task Content -->
                            <div class="flex-1">
                                <div class="flex flex-wrap items-center gap-3 mb-2">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white {{ $task->isCompleted() ? 'line-through text-gray-500 dark:text-gray-400' : '' }}">
                                        {{ $task->title }}
                                    </h3>
                                    
                                    <!-- Status Badge -->
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        {{ $task->status === 'completed' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : '' }}
                                        {{ $task->status === 'in_progress' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : '' }}
                                        {{ $task->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' : '' }}
                                        {{ $task->status === 'cancelled' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' : '' }}
                                    ">
                                        {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                    </span>
                                    
                                    <!-- Priority Badge -->
                                    <span class="px-2 py-1 text-xs font-medium rounded-full 
                                        {{ $task->priority === 'critical' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' : '' }}
                                        {{ $task->priority === 'high' ? 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300' : '' }}
                                        {{ $task->priority === 'normal' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : '' }}
                                        {{ $task->priority === 'low' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : '' }}
                                    ">
                                        {{ ucfirst($task->priority) }}
                                    </span>

                                    <!-- Category Badge -->
                                    @if($task->category)
                                        <span 
                                            class="px-2 py-1 text-xs font-medium rounded-full text-white"
                                            style="background-color: {{ $task->category->color }}"
                                        >
                                            {{ $task->category->name }}
                                        </span>
                                    @endif
                                </div>

                                @if($task->description)
                                    <p class="text-gray-600 dark:text-gray-400 mb-3 {{ $task->isCompleted() ? 'line-through' : '' }}">
                                        {{ $task->description }}
                                    </p>
                                @endif

                                <!-- Due Date -->
                                @if($task->due_date)
                                    <div class="flex items-center text-sm {{ $task->isOverdue() ? 'text-red-500 dark:text-red-400' : 'text-gray-500 dark:text-gray-400' }}">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        Due: {{ $task->due_date->format('M j, Y g:i A') }}
                                        @if($task->isOverdue())
                                            <span class="ml-2 px-2 py-1 bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 text-xs rounded-full">
                                                Overdue
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center space-x-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                            <!-- Status Actions based on current status -->
                            @if($task->status === 'pending')
                                <button 
                                    wire:click="markInProgress({{ $task->id }})"
                                    class="p-2 text-blue-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200"
                                    title="Mark as In Progress"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1.5a1.5 1.5 0 001.5-1.5V9a6 6 0 016 6v.5a1.5 1.5 0 01-1.5 1.5H14m-7-6V9a6 6 0 016-6v.5a1.5 1.5 0 011.5 1.5V10H14"></path>
                                    </svg>
                                </button>
                                <button 
                                    wire:click="markCompleted({{ $task->id }})"
                                    class="p-2 text-green-500 hover:text-green-600 dark:hover:text-green-400 transition-colors duration-200"
                                    title="Mark as Completed"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </button>
                            @elseif($task->status === 'in_progress')
                                <button 
                                    wire:click="markCompleted({{ $task->id }})"
                                    class="p-2 text-green-500 hover:text-green-600 dark:hover:text-green-400 transition-colors duration-200"
                                    title="Mark as Completed"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </button>
                                <button 
                                    wire:click="markPending({{ $task->id }})"
                                    class="p-2 text-yellow-500 hover:text-yellow-600 dark:hover:text-yellow-400 transition-colors duration-200"
                                    title="Mark as Pending"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </button>
                            @elseif($task->status === 'completed')
                                <button 
                                    wire:click="markPending({{ $task->id }})"
                                    class="p-2 text-yellow-500 hover:text-yellow-600 dark:hover:text-yellow-400 transition-colors duration-200"
                                    title="Reopen Task"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                </button>
                                <button 
                                    wire:click="markInProgress({{ $task->id }})"
                                    class="p-2 text-blue-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200"
                                    title="Mark as In Progress"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1.5a1.5 1.5 0 001.5-1.5V9a6 6 0 016 6v.5a1.5 1.5 0 01-1.5 1.5H14m-7-6V9a6 6 0 016-6v.5a1.5 1.5 0 011.5 1.5V10H14"></path>
                                    </svg>
                                </button>
                            @endif
                            
                            <button 
                                wire:click="editTask({{ $task->id }})"
                                class="p-2 text-gray-400 hover:text-blue-500 dark:hover:text-blue-400 transition-colors duration-200"
                                title="Edit Task"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                            
                            <button 
                                wire:click="deleteTask({{ $task->id }})"
                                class="p-2 text-gray-400 hover:text-red-500 dark:hover:text-red-400 transition-colors duration-200"
                                onclick="return confirm('Are you sure you want to delete this task?')"
                                title="Delete Task"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <svg class="mx-auto h-24 w-24 text-gray-300 dark:text-gray-600 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <h3 class="text-xl font-medium text-gray-900 dark:text-white mb-2">
                    @if($searchQuery || $statusFilter !== 'all' || $priorityFilter !== 'all' || $categoryFilter !== 'all')
                        No tasks match your filters
                    @else
                        No tasks yet
                    @endif
                </h3>
                <p class="text-gray-500 dark:text-gray-400 mb-6">
                    @if($searchQuery || $statusFilter !== 'all' || $priorityFilter !== 'all' || $categoryFilter !== 'all')
                        Try adjusting your search criteria or filters.
                    @else
                        Get started by creating your first task to stay organized and productive.
                    @endif
                </p>
                @if($searchQuery || $statusFilter !== 'all' || $priorityFilter !== 'all' || $categoryFilter !== 'all')
                    <button 
                        wire:click="clearFilters"
                        class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg shadow-sm hover:shadow-md transition-all duration-200"
                    >
                        Clear All Filters
                    </button>
                @else
                    <button 
                        wire:click="toggleCreateForm"
                        class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-sm hover:shadow-md transform hover:scale-105 transition-all duration-200"
                    >
                        Create Your First Task
                    </button>
                @endif
            </div>
        @endif
    </div>

    <!-- Categories Management Section -->
    @if($categories->count() > 0)
        <div class="mt-12">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Your Categories</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach($categories as $category)
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow duration-200">
                        <div class="flex items-center space-x-3">
                            <div 
                                class="w-4 h-4 rounded-full flex-shrink-0"
                                style="background-color: {{ $category->color }}"
                            ></div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                    {{ $category->name }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $category->tasks_count }} task{{ $category->tasks_count !== 1 ? 's' : '' }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>