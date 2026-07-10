<?php

namespace App\Models;

use App\Rules\ValidRut;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type',
        'name',
        'rut',
        'rut_raw',
        'email',
        'phone',
        'contact_person',
        'address',
        'city',
        'region',
        'website',
        'notes',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    // -------------------------------------------------------
    // Relaciones
    // -------------------------------------------------------

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function activeProjects(): HasMany
    {
        return $this->hasMany(Project::class)->where('status', 'active');
    }

    // -------------------------------------------------------
    // Accessors
    // -------------------------------------------------------

    public function getTypeNameAttribute(): string
    {
        return match ($this->type) {
            'company' => 'Empresa',
            'person'  => 'Persona Natural',
            default   => $this->type,
        };
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->type === 'company' && $this->contact_person) {
            return "{$this->name} ({$this->contact_person})";
        }

        return $this->name;
    }

    // -------------------------------------------------------
    // Scopes
    // -------------------------------------------------------

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('rut', 'like', "%{$term}%")
              ->orWhere('email', 'like', "%{$term}%")
              ->orWhere('contact_person', 'like', "%{$term}%");
        });
    }

    // -------------------------------------------------------
    // Mutators — formatea y normaliza el RUT al guardar
    // -------------------------------------------------------

    public function setRutAttribute(string $value): void
    {
        $clean = ValidRut::clean($value);
        $this->attributes['rut_raw'] = $clean;
        $this->attributes['rut']     = ValidRut::format($value);
    }
}
