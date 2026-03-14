<?php

namespace App\Models;

use App\Events\Todo\BoardCreated;
use App\Events\Todo\Subscriber\BoardSubscriberAdded;
use App\Events\Todo\Subscriber\BoardSubscriberRemoved;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Subscriber extends Pivot
{
    use HasFactory;

    const OWNER = 'owner';
    const GUEST = 'guest';
    const BOARD_OWNER_ROLE = self::OWNER;
    const DEFAULT_ROLE = self::GUEST;

    public $incrementing = true;

    protected $table = 'todo_subscribers';

    protected $fillable = [
        'role',
        'settings',
        'board_id',
        'user_id',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    protected static function booted()
    {
        static::created(function ($subscriber) {
            if ($subscriber->role != self::BOARD_OWNER_ROLE) {
                BoardSubscriberAdded::dispatch($subscriber, $subscriber->board);
            }

            if ($subscriber->role == self::BOARD_OWNER_ROLE) {
                BoardCreated::dispatch($subscriber->board);
            }
        });

        static::deleting(function ($subscriber) {
            BoardSubscriberRemoved::dispatch($subscriber, $subscriber->board);
        });
    }

    public function board()
    {
        return $this->belongsTo(Board::class, 'board_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getIsOwnerAttribute()
    {
        return $this->role == self::BOARD_OWNER_ROLE;
    }

    public function scopeBoardOwner($query)
    {
        $query->where('role', self::BOARD_OWNER_ROLE);
    }
}
