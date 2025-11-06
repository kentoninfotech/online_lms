<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'user_type',
        'profile',
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

    // Eloquent relationships
    public function parent(): HasOne
    {
        return $this->hasOne(ParentModel::class, 'user_id');
    }

    public function student(): HasOne
    {
        return $this->hasOne(Student::class, 'user_id');
    }

    public function instructor(): HasOne
    {
        return $this->hasOne(Instructor::class, 'user_id');
    }

    // Attendance records (for both students & instructors)
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'user_id');
    }

    // User → Subscriptions (if student)
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'student_id');
    }

    // Get the active subscription if any
    public function activeSubscription()
    {
        return $this->subscriptions()
            ->where('status', 'active')
            ->latest()
            ->first();
    }

    // Accessor for phone number based on user type - call as $user->phone_number
    public function getPhoneNumberAttribute()
    {
        if ($this->relationLoaded('parent') && $this->parent) {
            return $this->parent->number;
        }

        if ($this->relationLoaded('student') && $this->student) {
            return $this->student->number;
        }

        if ($this->relationLoaded('instructor') && $this->instructor) {
            return $this->instructor->number;
        }

        return null;
    }

    // Route notification for SMS channel
    public function routeNotificationForSms($notification)
    {
        return $this->phone_number;
    }


}
