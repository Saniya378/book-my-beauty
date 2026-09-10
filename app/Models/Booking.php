<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Booking extends Model
{
    use HasFactory;

    const UPDATED_AT = null;
    const CREATED_AT = 'created_at';
    protected $table = 'bookings';

    protected $fillable = [
        'customer_id',
        'service_id',
        'booking_date',
        'booking_time',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'booking_date' => 'date',
            'created_at' => 'datetime',
        ];
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Cancellation policy: can cancel within 2 hours of booking creation
     */
    public function canBeCancelled(): bool
    {
        if ($this->status === 'cancelled') {
            return false;
        }
        if (!$this->created_at) {
            return false;
        }
        return Carbon::now()->diffInHours($this->created_at) <= 2;
    }
}
