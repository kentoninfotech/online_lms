<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'subscription_id', 
        'parent_id', 
        'amount', 
        // 'method', 
        'file_path', 
        'status',
        'decision_reason'
    ];

    // Payment → Subscription
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    // Payment → Parent 
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ParentModel::class, 'parent_id');
    }
}
