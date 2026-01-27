<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Company extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains, HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'companies';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'domain',
        'database',
        'email',
        'phone',
        'address',
        'logo',
        'status',
        'subscription_start',
        'subscription_end',
        'settings',
        'created_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'settings' => 'array',
            'subscription_start' => 'date',
            'subscription_end' => 'date',
        ];
    }

    /**
     * Get the name of the "id" column.
     */
    public function getTenantKeyName(): string
    {
        return 'id';
    }

    /**
     * Get the value of the "id" column.
     */
    public function getTenantKey()
    {
        return $this->getAttribute($this->getTenantKeyName());
    }

    /**
     * Get the super admin who created this company
     */
    public function creator()
    {
        return $this->belongsTo(SuperAdmin::class, 'created_by');
    }

    /**
     * Check if company is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if subscription is valid
     */
    public function hasValidSubscription(): bool
    {
        if (!$this->subscription_end) {
            return true; // No expiry set
        }

        return $this->subscription_end >= now();
    }

    /**
     * Scope a query to only include active companies
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include companies with valid subscription
     */
    public function scopeValidSubscription($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('subscription_end')
                ->orWhere('subscription_end', '>=', now());
        });
    }

    /**
     * Get the database connection for this tenant.
     */
    public function getConnectionName()
    {
        return $this->database;
    }

    /**
     * Create the database for this tenant.
     */
    public static function booted()
    {
        static::creating(function ($company) {
            // Generate unique database name
            if (empty($company->database)) {
                $company->database = 'tenant_' . $company->slug . '_' . time();
            }
        });
    }
}
