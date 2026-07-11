<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;

class Server extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id',
        'created_by',
        'name',
        'type',
        'provider',
        'ip_address',
        'hostname',
        'os',
        'php_version',
        'db_type',
        'db_version',
        'panel',
        'web_server',
        'ssh_user',
        'ssh_port',
        'domain',
        'url',
        'status',
        'hosting_expires_at',
        'domain_expires_at',
        'ssl_expires_at',
        'credentials',
        'notes',
    ];

    protected $casts = [
        'hosting_expires_at' => 'date',
        'domain_expires_at'  => 'date',
        'ssl_expires_at'     => 'date',
        'ssh_port'           => 'integer',
    ];

    // Campos que NO se exponen nunca en JSON/arrays por defecto
    protected $hidden = ['credentials'];

    // ── Relaciones ──────────────────────────────────────────────

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Credenciales cifradas ────────────────────────────────────
    // Se cifran con la APP_KEY antes de guardar en BD.

    public function setCredentialsAttribute(?array $value): void
    {
        if (empty($value)) {
            $this->attributes['credentials'] = null;
            return;
        }
        // Solo guardar los que no estén vacíos
        $filtered = array_filter($value, fn ($v) => $v !== null && $v !== '');
        $this->attributes['credentials'] = $filtered
            ? Crypt::encryptString(json_encode($filtered))
            : null;
    }

    public function getCredentialsAttribute(?string $value): array
    {
        if (empty($value)) return [];
        try {
            return json_decode(Crypt::decryptString($value), true) ?? [];
        } catch (\Throwable) {
            return [];
        }
    }

    // ── Accessors ───────────────────────────────────────────────

    public function getTypeNameAttribute(): string
    {
        return match ($this->type) {
            'vps'       => 'VPS',
            'shared'    => 'Compartido',
            'managed'   => 'Administrado',
            'dedicated' => 'Dedicado',
            'cloud'     => 'Cloud',
            default     => 'Otro',
        };
    }

    public function getTypeColorAttribute(): string
    {
        return match ($this->type) {
            'vps'       => 'blue',
            'shared'    => 'gray',
            'managed'   => 'purple',
            'dedicated' => 'orange',
            'cloud'     => 'indigo',
            default     => 'gray',
        };
    }

    public function getStatusNameAttribute(): string
    {
        return match ($this->status) {
            'active'   => 'Activo',
            'inactive' => 'Inactivo',
            'expired'  => 'Vencido',
            default    => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'active'   => 'green',
            'inactive' => 'gray',
            'expired'  => 'red',
            default    => 'gray',
        };
    }

    public function getPanelNameAttribute(): string
    {
        return match ($this->panel) {
            'cpanel'   => 'cPanel',
            'plesk'    => 'Plesk',
            'forge'    => 'Laravel Forge',
            'ploi'     => 'Ploi',
            'runcloud' => 'RunCloud',
            'none'     => 'Sin panel',
            default    => 'Otro',
        };
    }

    public function getDbTypeNameAttribute(): string
    {
        return match ($this->db_type) {
            'mysql'      => 'MySQL',
            'postgresql' => 'PostgreSQL',
            'mariadb'    => 'MariaDB',
            'sqlite'     => 'SQLite',
            'other'      => 'Otro',
            default      => '—',
        };
    }

    // ── Alertas de vencimiento ───────────────────────────────────

    public function getHostingExpiryAlertAttribute(): ?string
    {
        if (! $this->hosting_expires_at) return null;
        $days = now()->diffInDays($this->hosting_expires_at, false);
        if ($days < 0)  return 'expired';
        if ($days <= 15) return 'critical';
        if ($days <= 30) return 'warning';
        return null;
    }

    public function getDomainExpiryAlertAttribute(): ?string
    {
        if (! $this->domain_expires_at) return null;
        $days = now()->diffInDays($this->domain_expires_at, false);
        if ($days < 0)  return 'expired';
        if ($days <= 15) return 'critical';
        if ($days <= 30) return 'warning';
        return null;
    }

    public function getSslExpiryAlertAttribute(): ?string
    {
        if (! $this->ssl_expires_at) return null;
        $days = now()->diffInDays($this->ssl_expires_at, false);
        if ($days < 0)  return 'expired';
        if ($days <= 10) return 'critical';
        if ($days <= 30) return 'warning';
        return null;
    }

    // ── Scopes ──────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpiringHosting($query, int $days = 30)
    {
        return $query->whereNotNull('hosting_expires_at')
                     ->where('hosting_expires_at', '<=', now()->addDays($days));
    }

    public function scopeExpiringDomain($query, int $days = 30)
    {
        return $query->whereNotNull('domain_expires_at')
                     ->where('domain_expires_at', '<=', now()->addDays($days));
    }
}
