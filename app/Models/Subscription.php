<?php



namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 
        'plan_id', 
        'start_date', 
        'end_date', 
        'status'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date'   => 'datetime',
    ];

    // Subscription → Student
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    // Subscription → Plan
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    // Subscription → Payments
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'subscription_id');
    }

    // Subscription → LessonReschedules
    public function reschedules(): HasMany
    {
        return $this->hasMany(RescheduleRequest::class);
    }

    // Check if the subscription is currently active
    public function isActive()
    {
        return $this->status === 'active' && $this->end_date >= now();
    }

    // Check if the subscription is in grace period
    public function isInGrace(): bool
    {
        return $this->status === 'grace';
    }

    // Check if the subscription has expired
    public function hasExpired(): bool
    {
        return now()->gt($this->end_date);
    }

    // Mark subscription as expired
    public function markExpired()
    {
        $this->update(['status' => 'expired']);
    }

    // Mark subscription as in grace period
    public function markGrace()
    {
        $this->update(['status' => 'grace']);
    }

    // Calculate remaining days in the subscription
    public function remainingDays(): int
    {
        return now()->lt($this->end_date) ? now()->diffInDays($this->end_date) : 0;
    }

    // Calculate the end date of the current billing cycle
    public function cycleEndDate(): Carbon
    {
        return $this->start_date->copy()->addDays($this->plan->getCycleLength());
    }

    // Check if the subscription is within the payment grace period
    public function inGracePeriod(): bool
    {
        return $this->end_date->copy()
            ->addDays($this->plan->payment_grace_days)
            ->gte(now());
    }

    // Calculate remaining reschedules in the current cycle
    public function remainingReschedules(): int
    {
        $used = $this->reschedules()
            ->whereBetween('created_at', [$this->start_date, $this->cycleEndDate()])
            ->count();

        return max(0, $this->plan->reschedule_limit - $used);
    }


    public function getDaysRemainingAttribute()
    {
        $graceDays = $this->plan->payment_grace_days 
            ?? (int) Setting::where('key', 'billing_grace_period_days')->value('value') ?? 7;

        $endWithGrace = Carbon::parse($this->end_date)->addDays($graceDays);
        return now()->diffInDays($endWithGrace, false);
    }

    public function getWarningMessageAttribute()
    {
        $warnDays = (int) Setting::where('key', 'subscription_expiry_warning_days')->value('value') ?? 7;

        if ($this->days_remaining <= $warnDays && $this->days_remaining > 0) {
            $days_left = ceil($this->days_remaining);
            return "⚠️ Your plan expires in {$days_left} days.";
            // return "⚠️ Your plan expires in {ceil($this->days_remaining)} days.";
        }

        if ($this->days_remaining === 0) {
            return "⚠️ Your plan expires today!";
        }

        if ($this->days_remaining < 0) {
            return "❌ Your plan has expired. Please renew to continue learning.";
        }

        return null;
    }

}
