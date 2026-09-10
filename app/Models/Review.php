<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    const UPDATED_AT = null;
    const CREATED_AT = 'created_at';
    protected $table = 'reviews';

    protected $fillable = [
        'customer_id',
        'beautician_id',
        'rating',
        'comment',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function beautician()
    {
        return $this->belongsTo(BeauticianProfile::class, 'beautician_id');
    }
}
