<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id',
        'user_id',
        'name',
        'code',
        'description',
        'status',
        'start_date',
        'end_date',
        'budget_amount',
        'currency',
        'budget_includes_vat',
        'progress',
        'notes',
    ];

    protected $casts = [
        'start_date'         => 'date',
        'end_date'           => 'date',
        'budget_includes_vat'=> 'boolean',
        'budget_amount'      => 'integer',
    ];

    // -------------------------------------------------------
    // Relaciones
    // -------------------------------------------------------

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    // -------------------------------------------------------
    // Accessors
    // -------------------------------------------------------

    public function getStatusNameAttribute(): string
    {
        return match ($this->status) {
            'draft'     => 'Borrador',
            'active'    => 'En progreso',
            'paused'    => 'Pausado',
            'completed' => 'Completado',
            'cancelled' => 'Cancelado',
            default     => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'draft'     => 'gray',
            'active'    => 'blue',
            'paused'    => 'yellow',
            'completed' => 'green',
            'cancelled' => 'red',
            default     => 'gray',
        };
    }

    public function getFormattedBudgetAttribute(): string
    {
        return match ($this->currency) {
            'CLP' => '$' . number_format($this->budget_amount, 0, ',', '.'),
            'USD' => 'USD ' . number_format($this->budget_amount / 100, 2, '.', ','),
            default => $this->budget_amount,
        };
    }

    // -------------------------------------------------------
    // Scopes
    // -------------------------------------------------------

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['active', 'paused']);
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('code', 'like', "%{$term}%")
              ->orWhereHas('client', fn ($c) => $c->where('name', 'like', "%{$term}%"));
        });
    }

    // -------------------------------------------------------
    // Helpers
    // -------------------------------------------------------

    public function recalculateProgress(): void
    {
        $total = $this->tasks()->count();

        if ($total === 0) {
            $this->update(['progress' => 0]);
            return;
        }

        $done = $this->tasks()->where('status', 'done')->count();
        $this->update(['progress' => (int) round(($done / $total) * 100)]);
    }

    protected static function booted(): void
    {
        static::creating(function (Project $project) {
            if (! $project->code) {
                $year  = now()->year;
                $count = static::whereYear('created_at', $year)->count() + 1;
                $project->code = sprintf('PM-%d-%03d', $year, $count);
            }
        });
    }
}
