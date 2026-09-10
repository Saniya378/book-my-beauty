<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BeauticianProfile extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'beautician_profiles';

    protected $fillable = [
        'user_id',
        'bio',
        'location',
        'phone',
        'profile_image',
        'profile_pic',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function services()
    {
        return $this->hasMany(Service::class, 'beautician_id');
    }

    public function activeServices()
    {
        return $this->hasMany(Service::class, 'beautician_id')->where('is_active', 1);
    }

    public function availability()
    {
        return $this->hasMany(Availability::class, 'beautician_id');
    }

    public function portfolioImages()
    {
        return $this->hasMany(PortfolioImage::class, 'beautician_id')->orderBy('uploaded_at', 'desc');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'beautician_id')->orderBy('created_at', 'desc');
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class, 'beautician_id');
    }

    public function getPhotoUrlAttribute(): string
    {
        if (!empty($this->profile_pic)) {
            return asset($this->profile_pic);
        }
        if (!empty($this->profile_image)) {
            return $this->profile_image;
        }
        return 'https://i.pravatar.cc/150?img=47';
    }

    public function getAverageRatingAttribute(): float
    {
        return (float) ($this->reviews()->avg('rating') ?: 0);
    }

    public function getReviewCountAttribute(): int
    {
        return (int) $this->reviews()->count();
    }
}
