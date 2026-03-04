<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Setting;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name', 
        'email', 
        'number', 
        'address',
        'gender',
        'dob',
    ];

    // Relationships, Accessors, Mutators, etc. can be added here
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Student → Lessons
    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class, 'student_id');
    }

    // Student → Parents (Many-to-Many)
    public function parents(): BelongsToMany
    {
        return $this->belongsToMany(ParentModel::class, 'parent_student', 'student_id', 'parent_id');
    }

    // Student → Subscriptions
    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class, 'student_id');
    }

    // Active or Grace Subscription with plan-specific grace period consideration
    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)
            ->with('plan')  // Eager load the plan relationship
            ->whereIn('status', ['active', 'grace'])
            ->whereDate('start_date', '<=', now())
            ->where(function ($query) {
                $query->whereDate('end_date', '>=', now())
                    ->orWhere(function ($sub) {
                        
                        // Fetch system default grace once outside the inner query
                        $systemGrace = (int) Setting::where('key', 'billing_grace_period_days')->value('value') ?? 7;

                        $sub->where('status', 'grace')
                            ->whereHas('plan') // Just confirm a plan exists
                            ->where(function ($inner) use ($systemGrace) {
                                
                                // Check against plan-specific grace period or system default
                                $inner->whereRaw('DATE(end_date) >= DATE_SUB(CURDATE(), INTERVAL COALESCE(
                                    (SELECT payment_grace_days FROM plans WHERE plans.id = subscriptions.plan_id), 
                                    ?
                                ) DAY)', [$systemGrace]);
                            });
                    });
            });
    }

    // Check if student has an active or grace subscription
    public function hasActiveSubscription() // turn to getHasActiveSubscriptionAttribute attribute accessor if you plan to use it as relation
    {
        // The activeSubscription() relationship returns a Subscription model if found, or null/empty if not.
        // By checking if the relationship result is truthy, you determine if an active/grace subscription exists.
        return (bool) $this->activeSubscription;
    }

}
