<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Availability extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'availability';

    protected $fillable = [
        'beautician_id',
        'day_of_week',
        'start_time',
        'end_time',
    ];

    public function beautician()
    {
        return $this->belongsTo(BeauticianProfile::class, 'beautician_id');
    }
}
