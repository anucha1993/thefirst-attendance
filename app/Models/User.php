<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // ============ Relationships ============

    /**
     * Driver's vehicles (one-to-many through vehicle_drivers pivot)
     */
    public function vehicleDrivers()
    {
        return $this->hasMany(VehicleDriver::class, 'driver_id');
    }

    /**
     * Get all vehicles assigned to this driver
     */
    public function vehicles()
    {
        return $this->belongsToMany(Vehicle::class, 'vehicle_drivers', 'driver_id', 'vehicle_id')
            ->withPivot('assigned_from', 'assigned_until', 'is_primary')
            ->withTimestamps();
    }

    /**
     * Trips driven by this user
     */
    public function trips()
    {
        return $this->hasMany(Trip::class, 'driver_id');
    }

    /**
     * Attendance audits created by this user
     */
    public function attendanceAudits()
    {
        return $this->hasMany(AttendanceAudit::class);
    }

    /**
     * If this user is an employee, get their employee record
     */
    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    // ============ Helper Methods ============

    /**
     * Check if user has a specific role
     */
    public function hasRole($role)
    {
        return $this->role === $role;
    }

    /**
     * Check if user has any of the given roles
     */
    public function hasAnyRole($roles)
    {
        return in_array($this->role, (array) $roles);
    }

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user is driver
     */
    public function isDriver()
    {
        return $this->hasRole('driver');
    }

    /**
     * Check if user is supervisor/HR/accounting
     */
    public function isSupervisor()
    {
        return $this->hasRole('supervisor');
    }

    /**
     * Check if user is employee
     */
    public function isEmployee()
    {
        return $this->hasRole('employee');
    }
}
