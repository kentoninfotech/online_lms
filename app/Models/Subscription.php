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
        return $this->belongsTo(User::class, 'student_id');
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
        return $this->hasMany(LessonReschedule::class, 'subscription_id');
    }

    // Check if the subscription is currently active
    public function isActive()
    {
        return $this->status === 'active' && $this->end_date >= now();
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

}
