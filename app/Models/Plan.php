<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'price', 
        'duration_type', // daily | weekly | monthly
        'duration_count', // e.g. 1, 3, 6
        'reschedule_limit',
        'payment_grace_days',
        'features'
    ];

    // Plan → Subscriptions
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    // Get the duration in days for scheduling horizon
    public function getHorizonDays(): int
    {
        return match ($this->duration_type) {
            'daily'   => $this->duration_count * 7,
            'weekly'  => $this->duration_count * 30,
            'monthly' => $this->duration_count * 90,
            default   => 30,
        };
    }

    // Get the cycle length in days for subscription renewals
    public function getCycleLength(): int
    {
        return match ($this->duration_type) {
            'daily'   => $this->duration_count * 1,
            'weekly'  => $this->duration_count * 7,
            'monthly' => $this->duration_count * 30,
            default   => 30,
        };
    }
}
