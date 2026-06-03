<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'event_date',
        'location',
        'quota',
    ];

    protected $casts = [
        'event_date' => 'datetime',
    ];

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }
}
