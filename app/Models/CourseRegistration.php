<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseRegistration extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'course_id',
        'nama_lengkap',
        'ttl',
        'tempat_tinggal',
        'gender',
        'price',
        'final_price',
        'discount_code',
        'status',
        'progress',
        'enrolled_at',
        'completed_at'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'final_price' => 'decimal:2',
        'progress' => 'decimal:2',
        'enrolled_at' => 'datetime',
        'completed_at' => 'datetime'
    ];

    /**
     * Get the user that owns the course registration.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the course that owns the registration.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get formatted price attribute
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp' . number_format($this->price, 0, ',', '.');
    }

    /**
     * Get formatted final price attribute
     */
    public function getFormattedFinalPriceAttribute(): string
    {
        return 'Rp' . number_format($this->final_price, 0, ',', '.');
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'paid' => 'bg-green-100 text-green-800',
            'pending' => 'bg-yellow-100 text-yellow-800',
            'cancelled' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Get progress percentage
     */
    public function getProgressPercentageAttribute(): string
    {
        return number_format($this->progress, 1) . '%';
    }

    /**
     * Check if registration is completed
     */
    public function getIsCompletedAttribute(): bool
    {
        return $this->progress >= 100;
    }

    /**
     * Scope approved registrations
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope pending registrations
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}