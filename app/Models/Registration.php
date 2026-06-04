<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event_id',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    protected static function booted()
    {
        static::created(function ($registration) {
            if ($registration->status !== 'Rejected') {
                $registration->event()->decrement('quota');
            }
        });

        static::updated(function ($registration) {
            if ($registration->isDirty('status')) {
                if ($registration->status === 'Rejected') {
                    $registration->event()->increment('quota');
                }
                if ($registration->getOriginal('status') === 'Rejected' && $registration->status !== 'Rejected') {
                    $registration->event()->decrement('quota');
                }
            }
        });

        static::deleted(function ($registration) {
            if ($registration->status !== 'Rejected') {
                $registration->event()->increment('quota');
            }
        });
    }
}
