<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class PointTransaction extends Model
{
    use HasFactory;

    protected $fillable = ['customer_id', 'points', 'type']; // Store type (add/deduct)

    protected static function booted()
    {
        static::addGlobalScope('owned', function (Builder $query) {
            if (Auth::check()) {
                $query->whereHas('customer', function ($q) {
                    $q->where('business_id', Auth::id());
                });
            }
        });

        static::creating(function ($transaction) {
            $customer = $transaction->customer;

            if ($customer) {
                if ($transaction->type === 'add') {
                    $customer->increment('total_points', $transaction->points);
                } elseif ($transaction->type === 'deduct') {
                    $customer->decrement('total_points', $transaction->points);
                    if ($customer->total_points < 0) {
                        $customer->update(['total_points' => 0]); // Prevent negative points
                    }
                }
            }
        });
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function scopeOwnedBy($query, $userId)
    {
        return $query->whereHas('customer', function ($q) use ($userId) {
            $q->where('business_id', $userId);
        });
    }
}
