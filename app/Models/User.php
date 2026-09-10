<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    const UPDATED_AT = null;
    const CREATED_AT = 'created_at';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'otp_code',
        'otp_expires',
        'failed_attempts',
        'locked_until',
        'is_active',
        'reset_token',
        'reset_expires',
    ];

    protected $hidden = [
        'password',
        'reset_token',
        'otp_code',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'locked_until' => 'datetime',
            'otp_expires' => 'datetime',
            'reset_expires' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function beauticianProfile()
    {
        return $this->hasOne(BeauticianProfile::class, 'user_id');
    }

    public function customerBookings()
    {
        return $this->hasMany(Booking::class, 'customer_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'customer_id');
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class, 'customer_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isBeautician(): bool
    {
        return $this->role === 'beautician';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    public function isLocked(): bool
    {
        return !empty($this->locked_until) && $this->locked_until->isFuture();
    }
}
