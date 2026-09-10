<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortfolioImage extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'portfolio_images';

    protected $fillable = [
        'beautician_id',
        'image_path',
        'caption',
        'uploaded_at',
    ];

    protected function casts(): array
    {
        return [
            'uploaded_at' => 'datetime',
        ];
    }

    public function beautician()
    {
        return $this->belongsTo(BeauticianProfile::class, 'beautician_id');
    }

    public function getUrlAttribute(): string
    {
        return asset($this->image_path);
    }
}
