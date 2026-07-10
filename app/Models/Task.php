<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id',
        'assigned_to',
        'title',
        'description',
        'category',
        'status',
        'priority',
        'due_date',
        'estimated_hours',
        'actual_hours',
        'order',
        'notes',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    // -------------------------------------------------------
    // Relaciones
    // -------------------------------------------------------

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // -------------------------------------------------------
    // Accessors
    // -------------------------------------------------------

    public function getCategoryNameAttribute(): string
    {
        return match ($this->category) {
            'development'   => 'Desarrollo',
            'design'        => 'Diseño',
            'server'        => 'Servidor',
            'testing'       => 'Testing',
            'documentation' => 'Documentación',
            'meeting'       => 'Reunión',
            'other'         => 'Otro',
            default         => $this->category,
        };
    }

    public function getCategoryColorAttribute(): string
    {
        return match ($this->category) {
            'development'   => 'blue',
            'design'        => 'purple',
            'server'        => 'orange',
            'testing'       => 'green',
            'documentation' => 'gray',
            'meeting'       => 'yellow',
            'other'         => 'gray',
            default         => 'gray',
        };
    }

    public function getStatusNameAttribute(): string
    {
        return match ($this->status) {
            'backlog'     => 'Por hacer',
            'in_progress' => 'En progreso',
            'review'      => 'En revisión',
            'done'        => 'Hecho',
            default       => $this->status,
        };
    }

    public function getPriorityNameAttribute(): string
    {
        return match ($this->priority) {
            'low'      => 'Baja',
            'medium'   => 'Media',
            'high'     => 'Alta',
            'critical' => 'Crítica',
            default    => $this->priority,
        };
    }

    public function getPriorityColorAttribute(): string
    {
        return match ($this->priority) {
            'low'      => 'green',
            'medium'   => 'yellow',
            'high'     => 'orange',
            'critical' => 'red',
            default    => 'gray',
        };
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date
            && $this->due_date->isPast()
            && $this->status !== 'done';
    }

    // -------------------------------------------------------
    // Scopes
    // -------------------------------------------------------

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                     ->where('status', '!=', 'done');
    }

    // -------------------------------------------------------
    // Observers — actualiza progreso del proyecto al cambiar estado
    // -------------------------------------------------------

    protected static function booted(): void
    {
        static::saved(function (Task $task) {
            $task->project->recalculateProgress();
        });

        static::deleted(function (Task $task) {
            $task->project->recalculateProgress();
        });
    }
}
