<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'type',
        'instructor_id',
        'price',
        'discount_code',
        'discount_percent',
        'min_quota',
        'max_quota',
        'current_enrollment',
        'is_active',
        'image_url'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'min_quota' => 'integer',
        'max_quota' => 'integer',
        'current_enrollment' => 'integer',
        'is_active' => 'boolean'
    ];

    /**
     * Get the instructor for the course
     */
    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    /**
     * Get the course registrations
     */
    public function registrations()
    {
        return $this->hasMany(CourseRegistration::class);
    }

    /**
     * Get active registrations
     */
    public function activeRegistrations()
    {
        return $this->hasMany(CourseRegistration::class)->where('status', 'paid');
    }

    /**
     * Get materials for the course
     */
    public function materials()
    {
        return $this->hasMany(CourseMaterial::class);
    }

    /**
     * Get assignments for the course
     */
    public function assignments()
    {
        return $this->hasMany(CourseAssignment::class);
    }

    /**
     * Check if course has available slots
     */
    public function hasAvailableSlots()
    {
        return $this->current_enrollment < $this->max_quota;
    }

    /**
     * Get final price after discount
     */
    public function getFinalPriceAttribute()
    {
        if ($this->discount_percent > 0) {
            return $this->price * (1 - ($this->discount_percent / 100));
        }
        return $this->price;
    }

    /**
     * Get formatted final price
     */
    public function getFormattedFinalPriceAttribute()
    {
        return 'Rp' . number_format($this->final_price, 0, ',', '.');
    }

    /**
     * Get formatted original price
     */
    public function getFormattedPriceAttribute()
    {
        return 'Rp' . number_format($this->price, 0, ',', '.');
    }

    /**
     * Scope active courses
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }
}