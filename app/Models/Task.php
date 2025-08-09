<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'description',
        'due_date',
        'priority',
        'status',
    ];

    protected $casts = [
        'due_date' => 'datetime',
    ];

     
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
 
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeHighPriority($query)
    {
        return $query->where('priority', 'high');
    }
 
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast() && !$this->isCompleted();
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'high' => 'red',
            'normal' => 'blue',
            'low' => 'green',
            'critical' => 'purple',
            default => 'gray'
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'completed' => 'green',
            'in_progress' => 'blue',
            'pending' => 'yellow',
            'cancelled' => 'red',
            default => 'gray'
        };
    }
}