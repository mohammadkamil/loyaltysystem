<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Customer extends Model
{
    use HasFactory, LogsActivity;

    protected static function booted()
    {
        static::addGlobalScope('owned', function (Builder $query) {
            if (Auth::check()) {
                $query->where('business_id', Auth::id());
            }
        });
    }

    protected $fillable = ['name', 'email', 'phone', 'total_points', 'business_id'];

    /**
     * Relationship: A customer belongs to a business (user).
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(User::class, 'business_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(PointTransaction::class);
    }

    public function scopeOwnedBy($query, $userId)
    {
        return $query->where('business_id', $userId);
    }

    public function issuePoints($points)
    {
        // Create transaction history
        $this->transactions()->create([
            'points' => $points,
            'type' => 'add',
        ]);

        // Update total points
        $this->increment('total_points', $points);

    }

    public function deductPoints($points)
    {
        if ($this->total_points < $points) {
            throw new \Exception("Not enough points.");
        }

        // Create transaction history
        $this->transactions()->create([
            'points' => -$points,
            'type' => 'deduct',
        ]);

        // Update total points
        $this->decrement('total_points', $points);

       
    }

    /**
     * Configure Spatie Activity Log
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'phone', 'total_points'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
