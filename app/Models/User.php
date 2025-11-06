<?php

namespace App\Models;

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
        'is_admin',
        'age_range',
        'education_level',
        'location',
        'phone',
        'date_of_birth',
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
            'is_admin' => 'boolean',
            'date_of_birth' => 'date',
        ];
    }

    /**
     * Get the course registrations for the user.
     */
    public function courseRegistrations()
    {
        return $this->hasMany(CourseRegistration::class);
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    /**
     * Get user's initial for avatar
     */
    public function getInitialAttribute(): string
    {
        return strtoupper(substr($this->name, 0, 1));
    }

    /**
     * Get formatted created date
     */
    public function getJoinedDateAttribute(): string
    {
        return $this->created_at->format('M d, Y');
    }

    /**
     * Get user's status badge class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return $this->is_admin ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800';
    }

    /**
     * Get user's status text
     */
    public function getStatusTextAttribute(): string
    {
        return $this->is_admin ? 'Admin' : 'User';
    }

    /**
     * Get user's course count
     */
    public function getCourseCountAttribute(): int
    {
        return $this->courseRegistrations->count();
    }

    /**
     * Scope filter by age range
     */
    public function scopeByAgeRange($query, $range)
    {
        return $query->where('age_range', $range);
    }

    /**
     * Scope filter by education level
     */
    public function scopeByEducationLevel($query, $level)
    {
        return $query->where('education_level', $level);
    }

    /**
     * Scope filter by location
     */
    public function scopeByLocation($query, $location)
    {
        return $query->where('location', 'like', '%'.$location.'%');
    }
}