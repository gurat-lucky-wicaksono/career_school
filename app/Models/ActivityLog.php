<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'activity_logs';

    /**
     * Disable updated_at timestamp since logs are immutable.
     */
    const UPDATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'role',
        'action',
        'description',
        'subject_type',
        'subject_id',
        'properties',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'properties' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Actor / User who performed the action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Target polymorphic model affected by the action.
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope query to search description, user name, action, or IP address.
     */
    public function scopeSearch(Builder $query, ?string $keyword): Builder
    {
        if (empty($keyword)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($keyword) {
            $q->where('description', 'like', "%{$keyword}%")
              ->orWhere('action', 'like', "%{$keyword}%")
              ->orWhere('ip_address', 'like', "%{$keyword}%")
              ->orWhereHas('user', function (Builder $userQuery) use ($keyword) {
                  $userQuery->where('name', 'like', "%{$keyword}%")
                            ->orWhere('email', 'like', "%{$keyword}%")
                            ->orWhere('username', 'like', "%{$keyword}%");
              });
        });
    }

    /**
     * Scope query to filter by user role.
     */
    public function scopeFilterRole(Builder $query, ?string $role): Builder
    {
        if (empty($role) || $role === 'all') {
            return $query;
        }

        return $query->where('role', $role);
    }

    /**
     * Scope query to filter by action prefix / group.
     */
    public function scopeFilterAction(Builder $query, ?string $action): Builder
    {
        if (empty($action) || $action === 'all') {
            return $query;
        }

        if (str_ends_with($action, '.*')) {
            $prefix = substr($action, 0, -2);
            return $query->where('action', 'like', "{$prefix}.%");
        }

        return $query->where('action', $action);
    }

    /**
     * Scope query to filter within a date range.
     */
    public function scopeDateRange(Builder $query, ?string $startDate, ?string $endDate): Builder
    {
        if (!empty($startDate)) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if (!empty($endDate)) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        return $query;
    }

    /**
     * Get Lucide icon name for the activity.
     */
    public function getIconAttribute(): string
    {
        if (str_starts_with($this->action, 'auth.')) {
            return 'key-round';
        }
        if (str_starts_with($this->action, 'company.')) {
            return 'building-2';
        }
        if (str_starts_with($this->action, 'application.')) {
            return 'file-text';
        }
        if (str_starts_with($this->action, 'skill.')) {
            return 'award';
        }
        if (str_starts_with($this->action, 'report.')) {
            return 'file-spreadsheet';
        }
        if (str_starts_with($this->action, 'master.')) {
            return 'database';
        }

        return 'activity';
    }

    /**
     * Get badge color styling class based on action category.
     */
    public function getBadgeClassAttribute(): string
    {
        if (str_starts_with($this->action, 'auth.')) {
            return 'bg-slate-100 text-slate-700 dark:bg-neutral-800 dark:text-slate-300 border-slate-200/80';
        }
        if (str_contains($this->action, 'verified') || str_contains($this->action, 'diterima') || str_contains($this->action, 'endorsed')) {
            return 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border-emerald-200/60';
        }
        if (str_contains($this->action, 'ditolak') || str_contains($this->action, 'delete') || str_contains($this->action, 'batal')) {
            return 'bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-300 border-rose-200/60';
        }
        if (str_contains($this->action, 'wawancara') || str_contains($this->action, 'update')) {
            return 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-300 border-indigo-200/60';
        }
        if (str_starts_with($this->action, 'report.')) {
            return 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300 border-amber-200/60';
        }

        return 'bg-sky-50 text-sky-700 dark:bg-sky-950/40 dark:text-sky-300 border-sky-200/60';
    }

    /**
     * Get friendly Indonesian label for the action.
     */
    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            'auth.login' => 'Login Sistem',
            'auth.logout' => 'Logout Sistem',
            'company.verified' => 'Verifikasi Industri',
            'company.rejected' => 'Penolakan Industri',
            'application.submitted' => 'Pengajuan Lamaran',
            'application.status_changed' => 'Perubahan Status Lamaran',
            'application.withdrawn' => 'Pembatalan Lamaran',
            'skill.validated' => 'Validasi Keahlian',
            'skill.bulk_validated' => 'Validasi Keahlian Massal',
            'report.exported_tracer' => 'Ekspor Tracer Study',
            'report.exported_applications' => 'Ekspor Data Lamaran',
            'report.printed_summary' => 'Cetak Laporan BKK',
            default => str_replace('.', ' - ', ucwords(str_replace('_', ' ', $this->action))),
        };
    }
}
