<?php

namespace App\Livewire;

use App\Models\Task;
use App\Models\Category;
use Livewire\Component;
use Livewire\Attributes\Rule;
use Carbon\Carbon;

class TaskManager extends Component
{
 
    public $title = '';
    public $description = '';
    public $due_date = '';
    public $priority = 'normal';
    public $category_id = '';

 
    #[Rule('required|min:2|max:100')]
    public $categoryName = '';
    
    #[Rule('required|regex:/^#[0-9A-F]{6}$/i')]
    public $categoryColor = '#3B82F6';

 
    public $editingTaskId = null;
 
    public $showCreateForm = false;
    public $showCategoryForm = false;
    public $statusFilter = 'all';
    public $priorityFilter = 'all';
    public $categoryFilter = 'all';
    public $searchQuery = '';

    public function createTask()
    {
        \Log::info('=== TASK CREATION STARTED ===');
        \Log::info('Form Data:', [
            'title' => $this->title,
            'description' => $this->description,
            'due_date' => $this->due_date,
            'priority' => $this->priority,
            'category_id' => $this->category_id,
        ]);

   
        if (!auth()->check()) {
            \Log::error('User not authenticated');
            session()->flash('error', 'Authentication required.');
            return;
        }
 
        try {
            \Log::info('Starting manual validation...');
            
            $validationRules = [
                'title' => 'required|min:3|max:255',
                'description' => 'nullable|max:1000',
                'due_date' => 'nullable',
                'priority' => 'required|in:low,normal,high,critical',
                'category_id' => 'nullable',
            ];
            
            $validator = \Validator::make([
                'title' => $this->title,
                'description' => $this->description,
                'due_date' => $this->due_date,
                'priority' => $this->priority,
                'category_id' => $this->category_id,
            ], $validationRules);
            
            if ($validator->fails()) {
                \Log::error('Manual validation failed:', $validator->errors()->toArray());
                $this->addError('form', 'Validation failed: ' . implode(', ', $validator->errors()->all()));
                return;
            }
            
            \Log::info('Manual validation passed successfully');
        } catch (\Exception $e) {
            \Log::error('Validation exception:', ['error' => $e->getMessage()]);
            $this->addError('form', 'Validation error: ' . $e->getMessage());
            return;
        }

        try {
         
            $taskData = [
                'user_id' => auth()->id(),
                'title' => trim($this->title),
                'description' => !empty(trim($this->description)) ? trim($this->description) : null,
                'priority' => $this->priority,
                'status' => 'pending',
                'due_date' => null,
                'category_id' => null,
            ];

            \Log::info('Basic task data prepared:', $taskData);
 
            if (!empty($this->due_date) && $this->due_date !== '') {
                try {
                    \Log::info('Processing due date:', ['raw_input' => $this->due_date]);
                    
            
                    $dateString = trim($this->due_date);
                    
                    if (strlen($dateString) === 16 && strpos($dateString, 'T') !== false) {
                  
                        $parsedDate = Carbon::createFromFormat('Y-m-d\TH:i', $dateString);
                    } elseif (strlen($dateString) === 19) {
                  
                        $parsedDate = Carbon::createFromFormat('Y-m-d H:i:s', $dateString);
                    } elseif (strlen($dateString) === 10) {
                        
                        $parsedDate = Carbon::createFromFormat('Y-m-d', $dateString)->startOfDay();
                    } else {
                        
                        $parsedDate = Carbon::parse($dateString);
                    }
                    
           
                    $taskData['due_date'] = $parsedDate->toDateTimeString();   
                    \Log::info('Due date parsed successfully:', [
                        'input' => $this->due_date,
                        'parsed' => $taskData['due_date']
                    ]);
                    
                } catch (\Exception $e) {
                    \Log::error('Due date parsing failed:', [
                        'input' => $this->due_date,
                        'error' => $e->getMessage()
                    ]);
                 
                    session()->flash('message', 'Task created successfully, but due date format was invalid.');
                }
            } else {
                \Log::info('No due date provided');
            }

          
            if (!empty($this->category_id) && $this->category_id !== '' && $this->category_id !== 'null' && $this->category_id !== null) {
                try {
                    $categoryId = (int) $this->category_id;
                    \Log::info('Processing category:', ['raw_input' => $this->category_id, 'parsed_id' => $categoryId]);
                    
                     
                    if ($categoryId > 0) {
                        
                        $category = Category::where('id', $categoryId)
                            ->where('user_id', auth()->id())
                            ->first();
                        
                        if ($category) {
                            $taskData['category_id'] = $categoryId;  
                            \Log::info('Category validated and assigned:', [
                                'category_id' => $categoryId,
                                'category_name' => $category->name
                            ]);
                        } else {
                            \Log::warning('Category not found or not owned by user:', [
                                'category_id' => $categoryId,
                                'user_id' => auth()->id()
                            ]);
                            session()->flash('message', 'Task created successfully, but selected category was invalid.');
                        }
                    } else {
                        \Log::info('Category ID is 0 or negative, treating as no category');
                    }
                } catch (\Exception $e) {
                    \Log::error('Category processing failed:', [
                        'input' => $this->category_id,
                        'error' => $e->getMessage()
                    ]);
                }
            } else {
                \Log::info('No category selected or category is empty/null');
            }

            \Log::info('Final task data before creation:', $taskData);

           
            $task = Task::create($taskData);
            \Log::info('Task created successfully:', ['task_id' => $task->id, 'task_title' => $task->title]);

             
            $this->resetTaskForm();
            $this->showCreateForm = false;
            
            session()->flash('message', 'Task created successfully!');
            \Log::info('=== TASK CREATION COMPLETED SUCCESSFULLY ===');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed:', ['errors' => $e->errors()]);
 
            
        } catch (\Exception $e) {
            \Log::error('Task creation failed with exception:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
                'form_data' => [
                    'title' => $this->title,
                    'description' => $this->description,
                    'priority' => $this->priority,
                    'due_date' => $this->due_date,
                    'category_id' => $this->category_id,
                ]
            ]);
            
            session()->flash('error', 'Failed to create task: ' . $e->getMessage());
        }
    }

     
    public function updateTask()
    {
        
        $validationRules = [
            'title' => 'required|min:3|max:255',
            'description' => 'nullable|max:1000',
            'due_date' => 'nullable',
            'priority' => 'required|in:low,normal,high,critical',
            'category_id' => 'nullable',
        ];
        
        $validator = \Validator::make([
            'title' => $this->title,
            'description' => $this->description,
            'due_date' => $this->due_date,
            'priority' => $this->priority,
            'category_id' => $this->category_id,
        ], $validationRules);
        
        if ($validator->fails()) {
            \Log::error('Update validation failed:', $validator->errors()->toArray());
            $this->addError('form', 'Validation failed: ' . implode(', ', $validator->errors()->all()));
            return;
        }

        $task = Task::where('user_id', auth()->id())->find($this->editingTaskId);
        
        if ($task) {
            $updateData = [
                'title' => trim($this->title),
                'description' => !empty(trim($this->description)) ? trim($this->description) : null,
                'priority' => $this->priority,
                'due_date' => null,
                'category_id' => null,
            ];

            
            if (!empty($this->due_date) && $this->due_date !== '') {
                try {
                    $dateString = trim($this->due_date);
                    if (strlen($dateString) === 16 && strpos($dateString, 'T') !== false) {
                        $parsedDate = Carbon::createFromFormat('Y-m-d\TH:i', $dateString);
                    } else {
                        $parsedDate = Carbon::parse($dateString);
                    }
                    $updateData['due_date'] = $parsedDate->toDateTimeString();  
                } catch (\Exception $e) {
                    \Log::warning('Due date parsing failed during update', ['input' => $this->due_date]);
                }
            }

 
            if (!empty($this->category_id) && $this->category_id !== '' && $this->category_id !== 'null' && $this->category_id !== null) {
                $categoryId = (int) $this->category_id;
                if ($categoryId > 0) {
                    $categoryExists = Category::where('id', $categoryId)
                        ->where('user_id', auth()->id())
                        ->exists();
                    
                    if ($categoryExists) {
                        $updateData['category_id'] = $categoryId; 
                    }
                }
            }

            $task->update($updateData);

            $this->resetTaskForm();
            $this->editingTaskId = null;
            $this->showCreateForm = false;
            
            session()->flash('message', 'Task updated successfully!');
        }
    }

 
    public function editTask($taskId)
    {
        $task = Task::where('user_id', auth()->id())->find($taskId);
        
        if ($task) {
            $this->editingTaskId = $task->id;
            $this->title = $task->title;
            $this->description = $task->description ?? '';
            $this->due_date = $task->due_date ? $task->due_date->format('Y-m-d\TH:i') : '';
            $this->priority = $task->priority;
            $this->category_id = $task->category_id ?? '';
            $this->showCreateForm = true;
        }
    }

  
    public function cancelEdit()
    {
        $this->editingTaskId = null;
        $this->resetTaskForm();
        $this->showCreateForm = false;
    }

  
    public function toggleTask($taskId)
    {
        $task = Task::where('user_id', auth()->id())->find($taskId);
        
        if ($task) {
            $newStatus = match($task->status) {
                'pending' => 'completed',
                'in_progress' => 'completed',
                'completed' => 'pending',
                default => 'pending'
            };
            
            $task->update(['status' => $newStatus]);
            
            $statusText = $newStatus === 'completed' ? 'completed' : 'reopened';
            session()->flash('message', "Task {$statusText}!");
        }
    }
 
    public function markInProgress($taskId)
    {
        $task = Task::where('user_id', auth()->id())->find($taskId);
        
        if ($task) {
            $task->update(['status' => 'in_progress']);
            session()->flash('message', 'Task marked as in progress!');
        }
    }

  
    public function markCompleted($taskId)
    {
        $task = Task::where('user_id', auth()->id())->find($taskId);
        
        if ($task) {
            $task->update(['status' => 'completed']);
            session()->flash('message', 'Task completed!');
        }
    }
 
    public function markPending($taskId)
    {
        $task = Task::where('user_id', auth()->id())->find($taskId);
        
        if ($task) {
            $task->update(['status' => 'pending']);
            session()->flash('message', 'Task marked as pending!');
        }
    }

 
    public function deleteTask($taskId)
    {
        $task = Task::where('user_id', auth()->id())->find($taskId);
        
        if ($task) {
            $task->delete();
            session()->flash('message', 'Task deleted successfully!');
        }
    }
 
    public function createCategory()
    {
        $this->validate([
            'categoryName' => 'required|min:2|max:100|unique:categories,name,NULL,id,user_id,' . auth()->id(),
            'categoryColor' => 'required|regex:/^#[0-9A-F]{6}$/i'
        ]);

        try {
            Category::create([
                'user_id' => auth()->id(),
                'name' => trim($this->categoryName),
                'color' => $this->categoryColor,
            ]);

            $this->resetCategoryForm();
            $this->showCategoryForm = false;
            
            session()->flash('message', 'Category created successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create category: ' . $e->getMessage());
        }
    }

 
    public function resetTaskForm()
    {
        $this->title = '';
        $this->description = '';
        $this->due_date = '';
        $this->priority = 'normal';
        $this->category_id = '';
        $this->resetValidation();
    }

    public function resetCategoryForm()
    {
        $this->categoryName = '';
        $this->categoryColor = '#3B82F6';
        $this->resetValidation(['categoryName', 'categoryColor']);
    }

   
    public function toggleCreateForm()
    {
        $this->showCreateForm = !$this->showCreateForm;
        if (!$this->showCreateForm) {
            $this->resetTaskForm();
            $this->editingTaskId = null;
        }
    }

    public function toggleCategoryForm()
    {
        $this->showCategoryForm = !$this->showCategoryForm;
        if (!$this->showCategoryForm) {
            $this->resetCategoryForm();
        }
    }

   
    public function clearFilters()
    {
        $this->statusFilter = 'all';
        $this->priorityFilter = 'all';
        $this->categoryFilter = 'all';
        $this->searchQuery = '';
    }
 
    private function getFilteredTasks()
    {
        $query = Task::where('user_id', auth()->id())->with('category');
 
        if ($this->searchQuery) {
            $query->where(function($q) {
                $q->where('title', 'like', '%' . $this->searchQuery . '%')
                  ->orWhere('description', 'like', '%' . $this->searchQuery . '%');
            });
        }
 
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }
 
        if ($this->priorityFilter !== 'all') {
            $query->where('priority', $this->priorityFilter);
        }
 
        if ($this->categoryFilter !== 'all') {
            if ($this->categoryFilter === 'none') {
                $query->whereNull('category_id');
            } else {
                $query->where('category_id', $this->categoryFilter);
            }
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function render()
    {
        $tasks = $this->getFilteredTasks();

        $categories = Category::where('user_id', auth()->id())
            ->withCount('tasks')
            ->orderBy('name')
            ->get();

        
        $allTasks = Task::where('user_id', auth()->id())->get();
        $stats = [
            'total' => $allTasks->count(),
            'completed' => $allTasks->where('status', 'completed')->count(),
            'pending' => $allTasks->where('status', 'pending')->count(),
            'in_progress' => $allTasks->where('status', 'in_progress')->count(),
            'overdue' => $allTasks->filter(function($task) {
                return $task->due_date && 
                       $task->due_date->isPast() && 
                       $task->status !== 'completed';
            })->count(),
        ];

        return view('livewire.task-manager', [
            'tasks' => $tasks,
            'categories' => $categories,
            'stats' => $stats,
        ]);
    }
}