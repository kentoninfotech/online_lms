<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'subscription_id', 
        'parent_id', 
        'amount', 
        'file_path', 
        'status'
    ];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }
}
