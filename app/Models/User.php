<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
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
        'phone',
        'user_type',
        'password',
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
        ];
    }

    // relationships
    // Parent → Students
    public function children()
    {
        return $this->belongsToMany(User::class, 'parent_student', 'parent_id', 'student_id');
    }

    // Student → Parent(s)
    public function parents()
    {
        return $this->belongsToMany(User::class, 'parent_student', 'student_id', 'parent_id');
    }

    // Instructor → Lessons
    public function lessons()
    {
        return $this->hasMany(Lesson::class, 'instructor_id');
    }

    // Student → Subscriptions
    public function subscriptions()
    {
        return $this->hasMany(StudentSubscription::class, 'student_id');
    }

    // Parent → Payments
    public function payments()
    {
        return $this->hasMany(Payment::class, 'parent_id');
    }

    // Attendance records (for both students & instructors)
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'user_id');
    }
}
