<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'services';

    protected $fillable = [
        'beautician_id',
        'service_name',
        'price',
        'duration',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'duration' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function beautician()
    {
        return $this->belongsTo(BeauticianProfile::class, 'beautician_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'service_id');
    }
}
