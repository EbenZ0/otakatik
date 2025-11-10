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
        'is_instructor',
        'age_range',
        'education_level',
        'location',
        'phone',
        'date_of_birth',
        'bio',
        'expertise'
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
            'is_instructor' => 'boolean',
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
     * Get the courses taught by the user (if instructor)
     */
    public function taughtCourses()
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }

    /**
     * Get assignment submissions for the user
     */
    public function assignmentSubmissions()
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    /**
     * Check if user is instructor
     */
    public function isInstructor(): bool
    {
        return $this->is_instructor;
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
        if ($this->is_admin) {
            return 'bg-purple-100 text-purple-800';
        } elseif ($this->is_instructor) {
            return 'bg-blue-100 text-blue-800';
        } else {
            return 'bg-green-100 text-green-800';
        }
    }

    /**
     * Get user's status text
     */
    public function getStatusTextAttribute(): string
    {
        if ($this->is_admin) {
            return 'Admin';
        } elseif ($this->is_instructor) {
            return 'Instructor';
        } else {
            return 'User';
        }
    }

    /**
     * Get user's course count
     */
    public function getCourseCountAttribute(): int
    {
        return $this->courseRegistrations->count();
    }

    /**
     * Get user's enrolled courses (approved)
     */
    public function getEnrolledCoursesAttribute()
    {
        return $this->courseRegistrations()->where('status', 'paid')->with('course')->get();
    }

    /**
     * Get user's pending course registrations
     */
    public function getPendingRegistrationsAttribute()
    {
        return $this->courseRegistrations()->where('status', 'pending')->with('course')->get();
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

    /**
     * Scope instructors only
     */
    public function scopeInstructors($query)
    {
        return $query->where('is_instructor', true);
    }
}